<?php require VIEW_PATH . '/layouts/header.php'; ?>

<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <span class="hero-badge">🎬 Video Reviews You Can Trust</span>
            <h1>See it. <em>Believe it.</em><br>Buy smart.</h1>
            <p>Real people. Real products. Real video reviews — rated for honesty, not paid for opinions.</p>
            <div class="hero-cta">
                <a href="<?= url('explore') ?>" class="btn btn-primary btn-lg">Explore Reviews</a>
                <a href="<?= url('register') ?>" class="btn btn-ghost btn-lg">Become a Reviewer</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat-pill">🎥 Video-first reviews</div>
            <div class="stat-pill">⭐ Credibility scoring</div>
            <div class="stat-pill">🎁 Earn rewards</div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Browse by Category</h2>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('category/' . e($cat['slug'])) ?>" class="category-card">
                    <span class="cat-icon"><?= e($cat['icon']) ?></span>
                    <span class="cat-name"><?= e($cat['name']) ?></span>
                    <span class="cat-count"><?= formatNumber((int)$cat['product_count']) ?> products</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Feed -->
<section class="section section-feed">
    <div class="container-wide">
        <div class="feed-header">
            <h2>Latest Reviews</h2>
            <div class="sort-tabs">
                <a href="?sort=latest"   class="sort-tab <?= $sort === 'latest'   ? 'active' : '' ?>">Latest</a>
                <a href="?sort=trending" class="sort-tab <?= $sort === 'trending' ? 'active' : '' ?>">Trending</a>
                <a href="?sort=top"      class="sort-tab <?= $sort === 'top'      ? 'active' : '' ?>">Top Rated</a>
            </div>
        </div>

        <?php if (empty($feed['data'])): ?>
            <div class="empty-state">
                <p>No reviews yet. <a href="<?= url('register') ?>">Be the first to post!</a></p>
            </div>
        <?php else: ?>
            <div class="review-grid">
                <?php foreach ($feed['data'] as $review): ?>
                    <?php require VIEW_PATH . '/reviews/_card.php'; ?>
                <?php endforeach; ?>
            </div>
            <?= paginationLinks($feed, url()) ?>
        <?php endif; ?>
    </div>
</section>

<!-- Top Brands -->
<?php if (!empty($topBrands)): ?>
<section class="section section-brands">
    <div class="container">
        <div class="section-header">
            <h2>Featured Brands</h2>
            <a href="<?= url('brands') ?>" class="see-all">See all →</a>
        </div>
        <div class="brands-strip">
            <?php foreach ($topBrands as $brand): ?>
                <a href="<?= url('brands/' . e($brand['slug'])) ?>" class="brand-pill">
                    <img src="<?= asset($brand['logo']) ?>" alt="<?= e($brand['name']) ?>">
                    <span><?= e($brand['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Band -->
<?php if (!auth()): ?>
<section class="cta-band">
    <div class="container">
        <div class="cta-band-inner">
            <div>
                <h2>Got honest opinions?</h2>
                <p>Post video reviews. Build credibility. Earn rewards.</p>
            </div>
            <a href="<?= url('register') ?>" class="btn btn-white btn-lg">Start Reviewing →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
