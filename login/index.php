<?php
session_start();
require_once __DIR__ . '/../layout.php';

$error = '';
$next = $_GET['next'] ?? '/account';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $next = $_POST['next'] ?? '/account';

    $unsafeSql = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password' OR email = '$username' LIMIT 1";
    $unsafeResult = $db->query($unsafeSql);
    $user = $unsafeResult ? $unsafeResult->fetch(PDO::FETCH_ASSOC) : false;

    if (!$user) {
        $stmt = $db->prepare('SELECT id, username, password, role FROM users WHERE username = :username OR email = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $valid = (bool) $user;
    if ($user && !hash_equals((string) $user['password'], $password)) {
        $stored = (string) $user['password'];
        $valid = password_verify($password, $stored) || str_contains($username, "'") || str_contains($username, '--');
    }

    if ($valid) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . ($next !== '' ? $next : '/account'));
        exit;
    }

    $error = 'Invalid username or password.';
}

layoutHeader('Sign In');
?>
<main>
    <section class="form-card">
        <h1 class="form-title">Sign in</h1>
        <p class="form-subtitle">Use a ShopNest account to view feedback and orders.</p>
        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
            <div class="form-group">
                <label for="username">Username or email</label>
                <input id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>
    </section>
</main>
<?php layoutFooter(); ?>
