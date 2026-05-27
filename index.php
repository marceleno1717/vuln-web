<?php
session_start();
require_once __DIR__ . '/layout.php';

$featured = $db->query("SELECT * FROM products ORDER BY RANDOM() LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

$icons = [
    'headphones' => '🎧', 'sneakers' => '👟', 'bottle' => '🧴',
    'keyboard' => '⌨️', 'wallet' => '👜', 'yogamat' => '🧘',
    'coffee' => '☕', 'stand' => '📱'
];

layoutHeader('Home');
?>

<main>
    <!-- HERO -->
    <section style="
        background: var(--text);
        border-radius: 24px;
        padding: 72px 60px;
        margin-bottom: 64px;
        display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;
        overflow: hidden; position: relative;
    ">
        <div style="position:relative;z-index:1;">
            <div style="display:inline-block;background:var(--accent);color:#fff;font-size:12px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;padding:6px 14px;border-radius:20px;margin-bottom:20px;">
                New Arrivals
            </div>
            <h1 style="font-family:'Fraunces',serif;font-size:52px;font-weight:700;color:#fff;line-height:1.1;margin-bottom:20px;">
                Discover Quality You Can Feel
            </h1>
            <p style="color:rgba(255,255,255,.6);font-size:17px;line-height:1.6;margin-bottom:36px;">
                Curated products for everyday living. From tech to wellness — everything you need, nothing you don't.
            </p>
            <div style="display:flex;gap:12px;">
                <a href="/products" class="btn btn-primary" style="padding:14px 32px;font-size:15px;border-radius:12px;">
                    Shop Now
                </a>
                <a href="/products?category=Electronics" class="btn" style="padding:14px 32px;font-size:15px;border-radius:12px;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);">
                    Explore Electronics
                </a>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <?php foreach (array_slice($featured, 0, 4) as $i => $p): ?>
            <a href="/product?id=<?= $p['id'] ?>" style="
                background:rgba(255,255,255,.05);
                border:1px solid rgba(255,255,255,.1);
                border-radius:16px; padding:20px;
                display:flex;flex-direction:column;align-items:center;gap:8px;
                transition:background .2s;
                <?= $i === 1 ? 'margin-top:24px;' : '' ?>
                <?= $i === 3 ? 'margin-top:-24px;' : '' ?>
            " onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='rgba(255,255,255,.05)'">
                <div style="font-size:40px;"><?= $icons[$p['image']] ?? '📦' ?></div>
                <div style="color:#fff;font-size:13px;font-weight:500;text-align:center;"><?= htmlspecialchars($p['name']) ?></div>
                <div style="color:var(--accent);font-size:14px;font-weight:600;">$<?= number_format($p['price'], 2) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SEARCH -->
    <section style="margin-bottom:64px;">
        <form action="/products" method="GET" class="search-bar" style="max-width:640px;margin:0 auto;">
            <input type="text" name="q" placeholder="Search for headphones, sneakers, coffee gear…">
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- CATEGORIES -->
    <section style="margin-bottom:64px;">
        <div class="page-header">
            <h1>Browse by Category</h1>
            <p>Find what you're looking for</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
            <?php
            $categories = [
                ['Electronics', '💻', '#EEF2FF'],
                ['Footwear', '👟', '#FFF3E0'],
                ['Lifestyle', '🌿', '#E8F5E9'],
                ['Fitness', '🏋️', '#FCE4EC'],
                ['Kitchen', '☕', '#FFF8E1'],
                ['Accessories', '🎒', '#F3E5F5'],
            ];
            foreach ($categories as [$cat, $icon, $color]):
            ?>
            <a href="/products?category=<?= urlencode($cat) ?>" style="
                background:<?= $color ?>;
                border-radius:16px; padding:28px 20px;
                display:flex;flex-direction:column;align-items:center;gap:10px;
                transition:transform .2s, box-shadow .2s;
                border: 1px solid transparent;
            " onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.1)'"
               onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="font-size:36px;"><?= $icon ?></div>
                <div style="font-weight:600;font-size:14px;"><?= $cat ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section>
        <div class="page-header">
            <h1>Featured Products</h1>
            <p>Hand-picked for you this week</p>
        </div>
        <div class="product-grid">
            <?php
            $products = $db->query("SELECT * FROM products LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($products as $p):
            ?>
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
    </section>
</main>

<?php layoutFooter(); ?>