<?php
require_once __DIR__ . '/db.php';

$host = $argv[1] ?? '127.0.0.1';
$port = (int) ($argv[2] ?? 8091);
$server = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
if (!$server) {
    fwrite(STDERR, "Could not start live update server: {$errstr}\n");
    exit(1);
}

echo "Live update server listening on {$host}:{$port}\n";

while ($client = @stream_socket_accept($server, -1)) {
    $request = fread($client, 4096);
    if (!preg_match('/Sec-WebSocket-Key:\s*(.+)\r\n/i', $request, $matches)) {
        fclose($client);
        continue;
    }

    $accept = base64_encode(sha1(trim($matches[1]) . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
    fwrite(
        $client,
        "HTTP/1.1 101 Switching Protocols\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept: {$accept}\r\n\r\n"
    );

    $message = websocket_read($client);
    $payload = json_decode($message, true);
    $orderId = (int) ($payload['order'] ?? 1001);

    $row = $db->query("
        SELECT orders.id, orders.status, orders.total, users.username, users.email
        FROM orders
        JOIN users ON users.id = orders.user_id
        WHERE orders.id = {$orderId}
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        websocket_write($client, json_encode([
            'type' => 'order_status',
            'order' => (int) $row['id'],
            'status' => $row['status'],
            'total' => (float) $row['total'],
            'customer' => $row['username'],
            'email' => $row['email'],
        ]));
    } else {
        websocket_write($client, json_encode(['type' => 'error', 'message' => 'Order not found']));
    }
    fclose($client);
}

function websocket_read($client): string {
    $header = fread($client, 2);
    if (strlen($header) < 2) {
        return '';
    }
    $bytes = unpack('Cfirst/Csecond', $header);
    $length = $bytes['second'] & 127;
    if ($length === 126) {
        $length = unpack('n', fread($client, 2))[1];
    } elseif ($length === 127) {
        $parts = unpack('N2', fread($client, 8));
        $length = ($parts[1] << 32) + $parts[2];
    }
    $mask = fread($client, 4);
    $data = fread($client, $length);
    $decoded = '';
    for ($i = 0; $i < strlen($data); $i++) {
        $decoded .= $data[$i] ^ $mask[$i % 4];
    }
    return $decoded;
}

function websocket_write($client, string $payload): void {
    $length = strlen($payload);
    if ($length <= 125) {
        $header = chr(129) . chr($length);
    } elseif ($length <= 65535) {
        $header = chr(129) . chr(126) . pack('n', $length);
    } else {
        $header = chr(129) . chr(127) . pack('NN', 0, $length);
    }
    fwrite($client, $header . $payload);
}
