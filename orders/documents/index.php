<?php
session_start();
require_once __DIR__ . '/../../layout.php';

$file = $_GET['file'] ?? 'invoice-1001.txt';
$basePath = __DIR__ . '/files/';
$content = '';
$error = '';

if ($file !== '') {
    $path = $basePath . $file;
    if (is_readable($path)) {
        $content = file_get_contents($path);
    } else {
        $error = 'Document not available.';
    }
}

layoutHeader('Order Documents');
?>
<main>
    <section class="page-header">
        <h1>Order Documents</h1>
        <p>View invoices, delivery notes, and partner attachments.</p>
    </section>

    <section class="form-card" style="max-width:760px;">
        <form method="GET" action="/orders/documents">
            <div class="form-group">
                <label for="file">Document</label>
                <select id="file" name="file">
                    <option value="invoice-1001.txt" <?= $file === 'invoice-1001.txt' ? 'selected' : '' ?>>Invoice SHOP-1001</option>
                    <option value="delivery-note-1001.txt" <?= $file === 'delivery-note-1001.txt' ? 'selected' : '' ?>>Delivery note SHOP-1001</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Open Document</button>
        </form>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-top:24px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($content !== ''): ?>
        <pre style="white-space:pre-wrap;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px;margin-top:24px;color:var(--text);font-family:monospace;font-size:14px;"><?= htmlspecialchars($content) ?></pre>
        <?php endif; ?>
    </section>
</main>
<?php layoutFooter(); ?>
