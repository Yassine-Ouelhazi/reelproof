<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px;padding-bottom:60px">
    <!-- Product header -->
    <div style="display:flex;gap:32px;margin-bottom:48px;flex-wrap:wrap;align-items:flex-start">
        <?php if (!empty($product['image'])): ?>
            <img src="<?= asset($product['image']) ?>" alt="<?= e($product['name']) ?>"
                 style="width:220px;height:220px;object-fit:cover;border-radius:var(--radius-lg);flex-shrink:0;border:1px solid var(--border)">
        <?php endif; ?>
        <div style="flex:1;min-width:240px">
            <a href="<?= url('brands/' . e($product['brand_slug'])) ?>" style="color:var(--accent);font-size:0.875rem">
                ← <?= e($product['brand_name']) ?>
            </a>
            <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin:10px 0"><?= e($product['name']) ?></h1>

            <?php if (!empty($product['category_name'])): ?>
                <span class="tag tag-cat" style="margin-bottom:12px;display:inline-block"><?= e($product['category_name']) ?></span>
            <?php endif; ?>

            <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:0.9rem;color:var(--text-dim);margin-bottom:16px">
                <span>🎬 <?= $product['review_count'] ?> reviews</span>
                <?php if ($product['avg_rating'] > 0): ?>
                    <span style="color:var(--accent);font-weight:600">⭐ <?= round($product['avg_rating'], 1) ?>/5</span>
                <?php endif; ?>
                <?php if ($product['price'] > 0): ?>
                    <span style="font-family:var(--font-display);font-size:1.1rem;color:var(--text)"><?= number_format($product['price'], 2) ?> TND</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['description'])): ?>
                <p style="color:var(--text-dim);line-height:1.7"><?= e($product['description']) ?></p>
            <?php endif; ?>

            <?php if (auth() && isReviewer()): ?>
                <a href="<?= url('reviews/create') ?>" class="btn btn-primary" style="margin-top:18px">
                    🎬 Review This Product
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews section -->
    <h2 style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:24px">
        Video Reviews (<?= count($reviews) ?>)
    </h2>

    <?php if (empty($reviews)): ?>
        <div class="empty-state">
            <p>No reviews yet for this product.</p>
            <?php if (auth() && isReviewer()): ?>
                <a href="<?= url('reviews/create') ?>" class="btn btn-primary" style="margin-top:12px">Be the first to review →</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="review-grid">
            <?php foreach ($reviews as $review): ?>
                <?php require VIEW_PATH . '/reviews/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
