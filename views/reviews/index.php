<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-wide" style="padding-top:40px;padding-bottom:60px">
    <div class="feed-header">
        <h1 style="font-family:var(--font-display);font-size:1.8rem">All Reviews</h1>
        <div class="sort-tabs">
            <a href="?sort=latest"   class="sort-tab <?= $sort === 'latest'   ? 'active' : '' ?>">🆕 Latest</a>
            <a href="?sort=trending" class="sort-tab <?= $sort === 'trending' ? 'active' : '' ?>">🔥 Trending</a>
            <a href="?sort=top"      class="sort-tab <?= $sort === 'top'      ? 'active' : '' ?>">⭐ Top Rated</a>
        </div>
    </div>

    <?php if (empty($feed['data'])): ?>
        <div class="empty-state"><p>No reviews yet.</p></div>
    <?php else: ?>
        <div class="review-grid">
            <?php foreach ($feed['data'] as $review): ?>
                <?php require VIEW_PATH . '/reviews/_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?= paginationLinks($feed, url('reviews')) ?>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
