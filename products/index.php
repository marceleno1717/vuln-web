<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../layout.php';

$icons = [
    'headphones' => '🎧', 'sneakers' => '👟', 'bottle' => '🧴',
    'keyboard' => '⌨️', 'wallet' => '👜', 'yogamat' => '🧘',
    'coffee' => '☕', 'stand' => '📱'
];

$q = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

if ($q !== '') {
    $products = $db->query("SELECT * FROM products WHERE name LIKE '%$q%' OR description LIKE '%$q%'")->fetchAll(PDO::FETCH_ASSOC);
} elseif ($category !== '') {
    $products = $db->query("SELECT * FROM products WHERE category = '$category'")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
}

layoutHeader('Shop');
?>
<main>
    <section style="margin-bottom:32px;">
        <form action="/products" method="GET" class="search-bar" style="max-width:560px;">
            <input type="text" name="q" value="<?= $q ?>" placeholder="Search products…">
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- Category pills -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:32px;">
        <a href="/products" class="btn btn-outline" style="padding:7px 18px;font-size:13px;border-radius:20px;">All</a>
        <?php foreach (['Electronics','Footwear','Lifestyle','Fitness','Kitchen','Accessories'] as $cat): ?>
        <a href="/products?category=<?= urlencode($cat) ?>"
           class="btn btn-outline"
           style="padding:7px 18px;font-size:13px;border-radius:20px;<?= $category === $cat ? 'background:var(--accent);color:#fff;border-color:var(--accent);' : '' ?>">
            <?= $cat ?>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if ($q !== ''): ?>
    <div style="margin-bottom:24px;color:var(--muted);font-size:14px;">
        Showing results for: <strong><?= $q ?></strong>
        (<?= count($products) ?> items found)
    </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 0;color:var(--muted);">
        <div style="font-size:48px;margin-bottom:16px;">🔍</div>
        <h3 style="font-family:'Fraunces',serif;margin-bottom:8px;">No products found</h3>
        <p>Try a different search term or browse all products.</p>
    </div>
    <?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
        <div class="product-card">
            <a href="/product?id=<?= $p['id'] ?>">
                <div class="product-img"><?= $icons[$p['image']] ?? '📦' ?></div>
            </a>
            <div class="product-body">
                <div class="product-category"><?= htmlspecialchars($p['category']) ?></div>
                <div class="product-name">
                    <a href="/product?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                </div>
                <div class="product-price">$<?= number_format($p['price'], 2) ?></div>
                <form method="POST" action="/cart">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
<?php layoutFooter(); ?>
