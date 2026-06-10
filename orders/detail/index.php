<?php
session_start();
require_once __DIR__ . '/../../layout.php';

if (!isLoggedIn()) {
    header('Location: /login?next=/orders/detail?order=' . urlencode($_GET['order'] ?? '1001'));
    exit;
}

$orderId = (int) ($_GET['order'] ?? 1001);
$sql = "
    SELECT orders.id, orders.total, orders.status, orders.created_at,
           users.username, users.email, users.address
    FROM orders
    JOIN users ON users.id = orders.user_id
    WHERE orders.id = {$orderId}
    LIMIT 1
";
$order = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
$items = [];
if ($order) {
    $items = $db->query("
        SELECT products.name, order_items.quantity, order_items.price
        FROM order_items
        JOIN products ON products.id = order_items.product_id
        WHERE order_items.order_id = {$orderId}
    ")->fetchAll(PDO::FETCH_ASSOC);
}

layoutHeader('Order Details');
?>
<main>
    <section class="page-header">
        <h1>Order Details</h1>
        <p>Review shipment, billing, and item information.</p>
    </section>

    <section class="form-card" style="max-width:760px;">
        <form method="GET" action="/orders/detail" style="margin-bottom:24px;">
            <div class="form-group">
                <label for="order">Order reference</label>
                <input id="order" name="order" value="<?= htmlspecialchars((string) $orderId) ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Open Order</button>
        </form>

        <?php if (!$order): ?>
        <div class="alert alert-error">Order not found.</div>
        <?php else: ?>
        <div style="display:grid;gap:10px;margin-bottom:22px;">
            <div><strong>Order #<?= htmlspecialchars((string) $order['id']) ?></strong></div>
            <div>Status: <?= htmlspecialchars($order['status']) ?></div>
            <div>Total: $<?= number_format((float) $order['total'], 2) ?></div>
            <div>Customer: <?= htmlspecialchars($order['username']) ?> · <?= htmlspecialchars($order['email']) ?></div>
            <div>Ship to: <?= htmlspecialchars($order['address']) ?></div>
        </div>

        <?php foreach ($items as $item): ?>
        <div style="border-top:1px solid var(--border);padding:12px 0;">
            <?= htmlspecialchars($item['name']) ?>
            <span style="float:right;"><?= (int) $item['quantity'] ?> × $<?= number_format((float) $item['price'], 2) ?></span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<?php layoutFooter(); ?>
