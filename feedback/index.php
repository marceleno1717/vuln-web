<?php
session_start();
require_once __DIR__ . '/../layout.php';

$topic = $_GET['topic'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $name = $_POST['name'] ?? 'guest';
    $comment = $_POST['comment'] ?? '';

    $stmt = $db->prepare('INSERT INTO feedback (name, comment) VALUES (:name, :comment)');
    $stmt->execute([':name' => $name, ':comment' => $comment]);
    $message = 'Feedback saved.';
}

$items = [];
if (isLoggedIn()) {
    $items = $db->query('SELECT name, comment, created_at FROM feedback ORDER BY id DESC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
}

layoutHeader('Customer Feedback');
?>
<main>
    <section class="page-header">
        <h1>Customer Feedback</h1>
        <p>Share product notes and campaign questions with ShopNest support.</p>
    </section>

    <section class="form-card" style="max-width:760px;margin-bottom:32px;">
        <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="GET" action="/feedback" style="margin-bottom:28px;">
            <div class="form-group">
                <label for="topic">Campaign topic</label>
                <input id="topic" name="topic" value="<?= $topic ?>" placeholder="summer sale">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Preview Topic</button>
        </form>

        <?php if ($topic !== ''): ?>
        <div style="margin-bottom:28px;color:var(--muted);font-size:14px;">
            Previewing topic: <strong><?= $topic ?></strong>
        </div>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
        <form method="POST" action="/feedback">
            <div class="form-group">
                <label for="name">Display name</label>
                <input id="name" name="name" value="<?= htmlspecialchars(currentUser() ?? 'guest') ?>" placeholder="guest">
            </div>
            <div class="form-group">
                <label for="comment">Feedback</label>
                <textarea id="comment" name="comment" rows="4" placeholder="Leave product feedback"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save Feedback</button>
        </form>
        <?php else: ?>
        <div class="alert alert-error">
            Sign in to save and view feedback.
        </div>
        <a class="btn btn-primary btn-block" href="/login?next=/feedback">Sign in</a>
        <?php endif; ?>
    </section>

    <section class="form-card" style="max-width:760px;margin-bottom:32px;">
        <h2 style="font-family:'Fraunces',serif;font-size:24px;margin-bottom:16px;">Live Preview</h2>
        <p style="color:var(--muted);margin-bottom:16px;">Hash preview renders local fragment content.</p>
        <div id="hash-preview" style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:16px;min-height:54px;"></div>
    </section>

    <section class="form-card" style="max-width:760px;">
        <h2 style="font-family:'Fraunces',serif;font-size:24px;margin-bottom:16px;">Recent Feedback</h2>
        <?php if (!isLoggedIn()): ?>
        <p style="color:var(--muted);">Feedback is visible to signed-in users only.</p>
        <?php elseif (empty($items)): ?>
        <p style="color:var(--muted);">No feedback yet.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
            <div style="border-bottom:1px solid var(--border);padding:14px 0;">
                <div style="font-weight:600;margin-bottom:6px;"><?= htmlspecialchars($item['name']) ?></div>
                <div><?= $item['comment'] ?></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<script>
const preview = document.getElementById('hash-preview');
preview.innerHTML = decodeURIComponent(location.hash.slice(1) || 'Add #preview text to this URL.');
</script>
<?php layoutFooter(); ?>
