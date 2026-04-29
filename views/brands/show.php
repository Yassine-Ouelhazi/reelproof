<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px;padding-bottom:60px">
    <!-- Brand Header -->
    <div style="display:flex;align-items:center;gap:24px;margin-bottom:40px;flex-wrap:wrap">
        <img src="<?= asset($brand['logo']) ?>" alt="<?= e($brand['name']) ?>"
             style="width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid var(--border)">
        <div>
            <h1 style="font-family:var(--font-display);font-size:2.2rem;font-weight:800"><?= e($brand['name']) ?></h1>
            <?php if (!empty($brand['description'])): ?>
                <p style="color:var(--text-dim);max-width:520px;margin-top:6px"><?= e($brand['description']) ?></p>
            <?php endif; ?>
            <div style="display:flex;gap:20px;margin-top:12px;font-size:0.875rem;color:var(--text-muted)">
                <span>📦 <?= $brand['product_count'] ?> products</span>
                <span>🎬 <?= $brand['review_count'] ?> reviews</span>
                <?php if ($brand['avg_rating'] > 0): ?>
                    <span>⭐ <?= round($brand['avg_rating'], 1) ?>/5 avg rating</span>
                <?php endif; ?>
                <?php if (!empty($brand['website'])): ?>
                    <a href="<?= e($brand['website']) ?>" target="_blank" rel="noopener"
                       style="color:var(--accent)">🌐 Website</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Products -->
    <?php if (!empty($products)): ?>
        <h2 style="font-family:var(--font-display);font-size:1.3rem;margin-bottom:18px">Products</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:48px">
            <?php foreach ($products as $product): ?>
                <a href="<?= url('products/' . e($product['slug'])) ?>"
                   style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;color:var(--text);display:block;transition:border-color 0.2s">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= asset($product['image']) ?>" alt="<?= e($product['name']) ?>"
                             style="width:100%;height:140px;object-fit:cover">
                    <?php endif; ?>
                    <div style="padding:12px">
                        <strong style="font-size:0.88rem"><?= e($product['name']) ?></strong>
                        <div style="display:flex;gap:10px;margin-top:6px;font-size:0.78rem;color:var(--text-muted)">
                            <span>🎬 <?= $product['review_count'] ?> reviews</span>
                            <?php if ($product['avg_rating'] > 0): ?>
                                <span>⭐ <?= round($product['avg_rating'], 1) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
