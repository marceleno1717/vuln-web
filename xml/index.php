<?php
session_start();
require_once __DIR__ . '/../layout.php';

if (!isLoggedIn()) {
    header('Location: /login?next=/xml');
    exit;
}

if (!isAdmin()) {
    http_response_code(403);
    layoutHeader('XML Import');
    ?>
    <main>
        <section class="form-card">
            <h1 class="form-title">Admin only</h1>
            <p class="form-subtitle">XML import is restricted to admin users.</p>
        </section>
    </main>
    <?php
    layoutFooter();
    exit;
}

$xml = $_POST['xml'] ?? '<order><id>SHOP-1001</id><note>standard delivery</note></order>';
$parsed = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LAB-ONLY XML entity expansion: intentionally resolves XML entities for authorized training.
    $expanded = $xml;
    if (preg_match_all('/<!ENTITY\s+([a-zA-Z0-9_-]+)\s+"([^"]*)"\s*>/', $xml, $entities, PREG_SET_ORDER)) {
        foreach ($entities as $entity) {
            $expanded = str_replace('&' . $entity[1] . ';', $entity[2], $expanded);
        }
    }

    if (!preg_match('/<order\b/i', $expanded)) {
        $error = 'Invalid XML.';
    } else {
        preg_match('/<id>(.*?)<\/id>/is', $expanded, $idMatch);
        preg_match('/<note>(.*?)<\/note>/is', $expanded, $noteMatch);
        $id = strip_tags($idMatch[1] ?? '');
        $note = strip_tags($noteMatch[1] ?? '');
        $parsed = "Order: {$id}\nNote: {$note}";
    }
}

layoutHeader('XML Import');
?>
<main>
    <section class="page-header">
        <h1>XML Import</h1>
        <p>Import partner order updates in XML format.</p>
    </section>

    <section class="form-card" style="max-width:760px;">
        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/xml">
            <div class="form-group">
                <label for="xml">Order XML</label>
                <textarea id="xml" name="xml" rows="8"><?= htmlspecialchars($xml) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Import XML</button>
        </form>
        <?php if ($parsed !== ''): ?>
        <pre style="white-space:pre-wrap;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px;margin-top:24px;color:var(--text);font-family:monospace;font-size:14px;"><?= htmlspecialchars($parsed) ?></pre>
        <?php endif; ?>
    </section>
</main>
<?php layoutFooter(); ?>
