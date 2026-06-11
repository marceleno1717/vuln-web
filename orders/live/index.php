<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../layout.php';

$httpHost = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8017';
$host = preg_replace('/:\d+$/', '', $httpHost);
$httpPort = str_contains($httpHost, ':') ? (int) substr(strrchr($httpHost, ':'), 1) : 80;
$wsUrl = 'ws://' . $host . ':' . ($httpPort + 1) . '/orders/live';

layoutHeader('Live Updates');
?>
<main>
    <section class="page-header">
        <h1>Live Updates</h1>
        <p>Watch order status changes as they arrive.</p>
    </section>

    <section class="form-card" style="max-width:760px;" data-ws-url="<?= htmlspecialchars($wsUrl) ?>">
        <form id="order-live-form" style="margin-bottom:24px;">
            <div class="form-group">
                <label for="order">Order reference</label>
                <input id="order" name="order" value="1001">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Watch Order</button>
        </form>
        <pre id="live-output" style="white-space:pre-wrap;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px;color:var(--text);font-family:monospace;font-size:14px;">Waiting for updates.</pre>
    </section>
</main>
<script>
const panel = document.querySelector('[data-ws-url]');
const output = document.getElementById('live-output');
document.getElementById('order-live-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const socket = new WebSocket(panel.dataset.wsUrl);
    socket.addEventListener('open', () => {
        socket.send(JSON.stringify({type: 'order_status', order: Number(document.getElementById('order').value || 1001)}));
    });
    socket.addEventListener('message', (message) => {
        output.textContent = message.data;
        socket.close();
    });
    socket.addEventListener('error', () => {
        output.textContent = 'Live updates are unavailable.';
    });
});
</script>
<?php layoutFooter(); ?>
