<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px;padding-bottom:60px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
        <h1 style="font-family:var(--font-display);font-size:2rem">Products</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="<?= url('products') ?>" class="tag <?= !$category ? 'tag-brand' : 'tag-product' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?= $cat['id'] ?>"
                   class="tag <?= $category == $cat['id'] ? 'tag-brand' : 'tag-product' ?>">
                    <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state"><p>No products found.</p></div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px">
            <?php foreach ($products as $product): ?>
                <a href="<?= url('products/' . e($product['slug'])) ?>"
                   style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;color:var(--text);display:block;transition:all 0.2s">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= asset($product['image']) ?>" alt="<?= e($product['name']) ?>"
                             style="width:100%;height:160px;object-fit:cover">
                    <?php else: ?>
                        <div style="width:100%;height:160px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:2.5rem">📦</div>
                    <?php endif; ?>
                    <div style="padding:16px">
                        <small style="color:var(--accent);font-size:0.75rem"><?= e($product['brand_name']) ?></small>
                        <strong style="display:block;font-family:var(--font-display);margin-top:4px"><?= e($product['name']) ?></strong>
                        <div style="display:flex;gap:12px;margin-top:8px;font-size:0.8rem;color:var(--text-muted)">
                            <span>🎬 <?= $product['review_count'] ?> reviews</span>
                            <?php if ($product['avg_rating'] > 0): ?>
                                <span>⭐ <?= round($product['avg_rating'], 1) ?></span>
                            <?php endif; ?>
                            <?php if ($product['price'] > 0): ?>
                                <span style="margin-left:auto;color:var(--text-dim)"><?= number_format($product['price'], 2) ?> TND</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
