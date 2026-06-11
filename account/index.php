<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../layout.php';

if (!isLoggedIn()) {
    header('Location: /login?next=/account');
    exit;
}

layoutHeader('Account');
?>
<main>
    <section class="form-card" style="max-width:720px;">
        <h1 class="form-title">My Account</h1>
        <p class="form-subtitle">Signed in as <?= htmlspecialchars(currentUser()) ?>.</p>
        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;">
            <a class="btn btn-outline" href="/feedback">View Feedback</a>
            <a class="btn btn-outline" href="/logout">Sign out</a>
        </div>
    </section>
</main>
<?php layoutFooter(); ?>
