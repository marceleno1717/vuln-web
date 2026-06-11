<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../layout.php';

$tracking = $_GET['tracking'] ?? '';
$output = '';

if ($tracking !== '') {
    $command = 'echo Shipment lookup for ' . $tracking;
    $output = shell_exec($command . ' 2>&1');
}

layoutHeader('Order Tracking');
?>
<main>
    <section class="page-header">
        <h1>Order Tracking</h1>
        <p>Check delivery updates for recent ShopNest orders.</p>
    </section>

    <section class="form-card" style="max-width:720px;">
        <form method="GET" action="/orders/track">
            <div class="form-group">
                <label for="tracking">Tracking reference</label>
                <input id="tracking" name="tracking" value="<?= htmlspecialchars($tracking) ?>" placeholder="SHOP-1001">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Check Status</button>
        </form>

        <?php if ($tracking !== ''): ?>
        <div style="margin-top:28px;">
            <h3 style="font-family:'Fraunces',serif;font-size:20px;margin-bottom:12px;">Lookup Result</h3>
            <pre style="white-space:pre-wrap;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px;color:var(--text);font-family:monospace;font-size:14px;"><?= htmlspecialchars(trim((string) $output)) ?></pre>
        </div>
        <?php endif; ?>
    </section>
</main>
<?php layoutFooter(); ?>
