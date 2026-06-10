<?php
require_once __DIR__ . '/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function isAdmin() {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function layoutHeader($title = 'ShopNest') {
    $user = currentUser();
    $cartCount = count($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — ShopNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #F7F4EF;
            --surface: #FFFFFF;
            --border: #E8E2D9;
            --text: #1A1714;
            --muted: #7A7168;
            --accent: #C8522A;
            --accent-hover: #A8421F;
            --accent-light: #F5EDE8;
            --success: #2D6A4F;
            --tag-bg: #EEE9E2;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
        }

        a { color: inherit; text-decoration: none; }

        /* NAV */
        nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 24px;
            display: flex; align-items: center; gap: 32px;
            height: 64px;
        }
        .nav-logo {
            font-family: 'Fraunces', serif;
            font-size: 22px; font-weight: 700;
            color: var(--text); letter-spacing: -0.5px;
        }
        .nav-logo span { color: var(--accent); }
        .nav-links {
            display: flex; gap: 4px; flex: 1;
        }
        .nav-links a {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px; font-weight: 400;
            color: var(--muted);
            transition: all .15s;
        }
        .nav-links a:hover { background: var(--tag-bg); color: var(--text); }
        .nav-actions {
            display: flex; align-items: center; gap: 12px;
        }
        .nav-actions a {
            font-size: 14px; color: var(--muted);
            padding: 6px 12px; border-radius: 8px;
            transition: all .15s;
        }
        .nav-actions a:hover { background: var(--tag-bg); color: var(--text); }
        .cart-btn {
            background: var(--accent) !important;
            color: #fff !important;
            padding: 8px 18px !important;
            border-radius: 20px !important;
            font-weight: 500 !important;
            display: flex; align-items: center; gap: 6px;
        }
        .cart-btn:hover { background: var(--accent-hover) !important; }
        .cart-count {
            background: #fff;
            color: var(--accent);
            border-radius: 50%;
            width: 18px; height: 18px;
            font-size: 11px; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
        }

        /* MAIN */
        main { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }

        /* SEARCH BAR */
        .search-bar {
            display: flex; gap: 0;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
        }
        .search-bar input {
            flex: 1; padding: 14px 20px;
            border: none; outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; background: transparent;
            color: var(--text);
        }
        .search-bar button {
            padding: 14px 24px;
            background: var(--accent);
            border: none; cursor: pointer;
            color: #fff; font-size: 14px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            transition: background .15s;
        }
        .search-bar button:hover { background: var(--accent-hover); }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }
        .product-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
        }
        .product-card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,.1);
            transform: translateY(-2px);
        }
        .product-img {
            width: 100%; aspect-ratio: 1;
            background: var(--tag-bg);
            display: flex; align-items: center; justify-content: center;
            font-size: 64px;
        }
        .product-body { padding: 16px; }
        .product-category {
            font-size: 11px; font-weight: 500;
            color: var(--muted); text-transform: uppercase;
            letter-spacing: .8px; margin-bottom: 6px;
        }
        .product-name {
            font-family: 'Fraunces', serif;
            font-size: 16px; font-weight: 600;
            line-height: 1.3; margin-bottom: 8px;
        }
        .product-price {
            font-size: 18px; font-weight: 600;
            color: var(--accent); margin-bottom: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px; font-weight: 500;
            cursor: pointer; border: none;
            font-family: 'DM Sans', sans-serif;
            transition: all .15s;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff; width: 100%; text-align: center;
        }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }
        .btn-outline:hover { background: var(--tag-bg); }

        /* FORMS */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            max-width: 480px; margin: 0 auto;
        }
        .form-title {
            font-family: 'Fraunces', serif;
            font-size: 28px; font-weight: 700;
            margin-bottom: 8px;
        }
        .form-subtitle { color: var(--muted); margin-bottom: 32px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 13px;
            font-weight: 500; margin-bottom: 6px;
            color: var(--muted);
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 10px; outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; background: var(--bg);
            color: var(--text);
            transition: border-color .15s;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--accent);
            background: var(--surface);
        }
        .btn-block { width: 100%; padding: 14px; font-size: 15px; border-radius: 12px; }

        /* ALERTS */
        .alert {
            padding: 14px 18px; border-radius: 10px;
            margin-bottom: 20px; font-size: 14px;
        }
        .alert-error { background: #FDECEA; color: #C0392B; border: 1px solid #F5C6C6; }
        .alert-success { background: #E8F5E9; color: #2D6A4F; border: 1px solid #C8E6C9; }

        /* PAGE HEADER */
        .page-header { margin-bottom: 32px; }
        .page-header h1 {
            font-family: 'Fraunces', serif;
            font-size: 36px; font-weight: 700;
            margin-bottom: 6px;
        }
        .page-header p { color: var(--muted); }

        /* FOOTER */
        footer {
            background: var(--text);
            color: rgba(255,255,255,.6);
            margin-top: 80px;
        }
        .footer-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 48px 24px 24px;
            display: grid; grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
        }
        .footer-brand .nav-logo { color: #fff; margin-bottom: 12px; }
        .footer-brand p { font-size: 14px; line-height: 1.6; }
        .footer-col h4 { color: #fff; font-size: 13px; font-weight: 500; margin-bottom: 16px; letter-spacing: .5px; text-transform: uppercase; }
        .footer-col a { display: block; font-size: 14px; margin-bottom: 8px; transition: color .15s; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.1);
            padding: 16px 24px; text-align: center;
            max-width: 1200px; margin: 0 auto;
            font-size: 13px;
        }

        /* BADGE */
        .badge {
            display: inline-block; padding: 3px 10px;
            border-radius: 20px; font-size: 11px;
            font-weight: 600; letter-spacing: .4px;
            text-transform: uppercase;
        }
        .badge-admin { background: var(--accent-light); color: var(--accent); }

        /* STARS */
        .stars { color: #F4A32A; letter-spacing: 2px; }
    </style>
</head>
<body>
<nav>
    <div class="nav-inner">
        <a href="/index" class="nav-logo">Shop<span>Nest</span></a>
        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/products">Shop</a>
            <a href="/products?category=Electronics">Electronics</a>
            <a href="/products?category=Lifestyle">Lifestyle</a>
            <a href="/products?category=Fitness">Fitness</a>
            <a href="/orders/track">Orders</a>
            <a href="/orders/documents">Documents</a>
            <a href="/orders/detail?order=1001">Order Details</a>
            <?php if ($user): ?>
                <a href="/feedback">Feedback</a>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
                <a href="/admin/import">Imports</a>
            <?php endif; ?>
        </div>
        <div class="nav-actions">
            <?php if ($user): ?>
                <a href="/account">👤 <?= htmlspecialchars($user) ?></a>
                <?php if (isAdmin()): ?>
                    <a href="/admin/reports">Reports</a>
                <?php endif; ?>
                <a href="/logout">Sign out</a>
            <?php else: ?>
                <a href="/login">Sign in</a>
                <a href="/register">Register</a>
                <a href="/orders/track">Track Order</a>
            <?php endif; ?>
            <a href="/cart" class="cart-btn">
                🛒 Cart
                <?php if ($cartCount > 0): ?>
                    <span class="cart-count"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>
<?php } ?>

<?php
function layoutFooter() {
?>
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="nav-logo">Shop<span style="color:#C8522A">Nest</span></div>
            <p>Your go-to destination for quality products at honest prices. Fast shipping, easy returns.</p>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <a href="/products?category=Electronics">Electronics</a>
            <a href="/products?category=Footwear">Footwear</a>
            <a href="/products?category=Lifestyle">Lifestyle</a>
            <a href="/products?category=Fitness">Fitness</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <a href="/account">My Orders</a>
            <a href="/login">Sign In</a>
            <a href="/register">Register</a>
        </div>
    </div>
    <div class="footer-bottom">
        © <?= date('Y') ?> ShopNest. All rights reserved.
    </div>
</footer>
</body>
</html>
<?php } ?>
