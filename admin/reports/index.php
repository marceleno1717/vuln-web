<?php
session_start();
require_once __DIR__ . '/../../layout.php';

if (!isLoggedIn()) {
    header('Location: /login?next=/admin/reports');
    exit;
}

$userCount = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$orderTotal = $db->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();
$latestUsers = $db->query('SELECT username, email, role, created_at FROM users ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

layoutHeader('Operations Reports');
?>
<main>
    <section class="page-header">
        <h1>Operations Reports</h1>
        <p>Daily account and order metrics.</p>
    </section>

    <section class="form-card" style="max-width:820px;">
        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:28px;">
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:18px;">
                <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Accounts</div>
                <div style="font-size:28px;font-weight:700;"><?= (int) $userCount ?></div>
            </div>
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:18px;">
                <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Booked revenue</div>
                <div style="font-size:28px;font-weight:700;">$<?= number_format((float) $orderTotal, 2) ?></div>
            </div>
        </div>

        <h2 style="font-family:'Fraunces',serif;font-size:22px;margin-bottom:14px;">Recent Accounts</h2>
        <?php foreach ($latestUsers as $row): ?>
        <div style="border-top:1px solid var(--border);padding:12px 0;">
            <strong><?= htmlspecialchars($row['username']) ?></strong>
            <span style="color:var(--muted);"> · <?= htmlspecialchars($row['email']) ?> · <?= htmlspecialchars($row['role']) ?></span>
        </div>
        <?php endforeach; ?>
    </section>
</main>
<?php layoutFooter(); ?>
