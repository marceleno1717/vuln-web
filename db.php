<?php
$db = new PDO('sqlite:' . __DIR__ . '/shop.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        email TEXT,
        password TEXT,
        role TEXT DEFAULT 'user',
        address TEXT,
        created_at TEXT DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        description TEXT,
        price REAL,
        category TEXT,
        image TEXT,
        stock INTEGER DEFAULT 100
    );
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        total REAL,
        status TEXT DEFAULT 'pending',
        created_at TEXT DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER,
        product_id INTEGER,
        quantity INTEGER,
        price REAL
    );
    CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER,
        user_id INTEGER,
        author TEXT,
        content TEXT,
        rating INTEGER,
        created_at TEXT DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS newsletter (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT,
        created_at TEXT DEFAULT (datetime('now'))
    );
    CREATE TABLE IF NOT EXISTS feedback (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        comment TEXT,
        created_at TEXT DEFAULT (datetime('now'))
    );
");

$count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($count == 0) {
    $db->exec("
        INSERT INTO users (username, email, password, role, address) VALUES
            ('admin', 'admin@shopnest.com', 'admin123', 'admin', '123 Admin St, NY'),
            ('john_doe', 'john@example.com', 'john1234', 'user', '456 Oak Ave, CA'),
            ('alice', 'alice@example.com', 'alice123', 'user', '789 Pine Rd, TX');

        INSERT INTO products (name, description, price, category, image, stock) VALUES
            ('Wireless Noise-Cancelling Headphones', 'Premium over-ear headphones with 30hr battery life, deep bass and crystal-clear highs. Compatible with all Bluetooth devices.', 89.99, 'Electronics', 'headphones', 120),
            ('Slim Fit Running Sneakers', 'Lightweight mesh sneakers with responsive foam sole. Available in multiple colors. Perfect for daily runs and casual wear.', 54.99, 'Footwear', 'sneakers', 200),
            ('Stainless Steel Water Bottle', 'Double-wall vacuum insulated bottle. Keeps drinks cold 24h, hot 12h. BPA-free, leak-proof lid.', 24.99, 'Lifestyle', 'bottle', 350),
            ('Mechanical Keyboard TKL', 'Tenkeyless mechanical keyboard with tactile brown switches, RGB backlight and aluminum frame. Great for typing and gaming.', 79.99, 'Electronics', 'keyboard', 80),
            ('Minimalist Leather Wallet', 'Slim genuine leather bifold wallet. 6 card slots, RFID blocking. Fits in any front pocket.', 34.99, 'Accessories', 'wallet', 180),
            ('Yoga Mat Pro', 'Non-slip 6mm thick yoga mat with alignment lines. Eco-friendly TPE material, includes carry strap.', 39.99, 'Fitness', 'yogamat', 150),
            ('Ceramic Pour-Over Coffee Set', 'Handcrafted ceramic dripper with matching mug. Perfect for specialty coffee lovers who enjoy the ritual.', 44.99, 'Kitchen', 'coffee', 90),
            ('Portable Phone Stand', 'Adjustable aluminum phone and tablet stand. Foldable, lightweight, compatible with all devices.', 19.99, 'Accessories', 'stand', 300);

        INSERT INTO reviews (product_id, user_id, author, content, rating) VALUES
            (1, 2, 'john_doe', 'Absolutely love these headphones. Sound quality is incredible for the price.', 5),
            (1, 3, 'alice', 'Great battery life and very comfortable. Noise cancellation works well.', 4),
            (2, 2, 'john_doe', 'Super comfortable for long runs. Lightweight and stylish.', 5),
            (3, 3, 'alice', 'Keeps my coffee hot all morning. Great build quality.', 5),
            (4, 2, 'john_doe', 'Typing on this is a joy. RGB looks amazing in the dark.', 4);
    ");
}

$orderCount = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
if ($orderCount == 0) {
    $db->exec("
        INSERT INTO orders (id, user_id, total, status, created_at) VALUES
            (1001, 2, 89.99, 'paid', datetime('now', '-7 days')),
            (1002, 3, 64.98, 'processing', datetime('now', '-2 days')),
            (1003, 1, 244.50, 'review', datetime('now', '-1 days'));

        INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
            (1001, 1, 1, 89.99),
            (1002, 3, 1, 24.99),
            (1002, 6, 1, 39.99),
            (1003, 4, 2, 79.99),
            (1003, 8, 1, 19.99);
    ");
}
?>
