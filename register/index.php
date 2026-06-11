<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../layout.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || strlen($password) < 4) {
        $error = 'Username, email, and password with 4+ chars required.';
    } else {
        $stmt = $db->prepare('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
        $ok = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => 'user',
        ]);

        if ($ok) {
            $_SESSION['user_id'] = (int) $db->lastInsertId();
            $_SESSION['user'] = $username;
            $_SESSION['role'] = 'user';
            header('Location: /account');
            exit;
        }

        $error = 'Username already exists.';
    }
}

layoutHeader('Register');
?>
<main>
    <section class="form-card">
        <h1 class="form-title">Create account</h1>
        <p class="form-subtitle">Register to view feedback and manage orders.</p>
        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/register">
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>
    </section>
</main>
<?php layoutFooter(); ?>
