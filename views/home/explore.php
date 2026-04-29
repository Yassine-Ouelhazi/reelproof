<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-wide" style="padding-top:40px; padding-bottom:60px">
    <div class="feed-header">
        <h1 style="font-family:var(--font-display);font-size:1.8rem">Explore Reviews</h1>
        <div class="sort-tabs">
            <a href="?sort=trending<?= $category ? '&category='.$category : '' ?>"
               class="sort-tab <?= $sort === 'trending' ? 'active' : '' ?>">🔥 Trending</a>
            <a href="?sort=latest<?= $category ? '&category='.$category : '' ?>"
               class="sort-tab <?= $sort === 'latest'   ? 'active' : '' ?>">🆕 Latest</a>
            <a href="?sort=top<?= $category ? '&category='.$category : '' ?>"
               class="sort-tab <?= $sort === 'top'      ? 'active' : '' ?>">⭐ Top Rated</a>
        </div>
    </div>

    <!-- Category filter strip -->
    <div class="category-filter-strip" style="margin-bottom:28px; display:flex; gap:8px; flex-wrap:wrap">
        <a href="?sort=<?= e($sort) ?>" class="tag <?= !$category ? 'tag-brand' : 'tag-product' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
            <a href="?sort=<?= e($sort) ?>&category=<?= $cat['id'] ?>"
               class="tag <?= $category == $cat['id'] ? 'tag-brand' : 'tag-product' ?>">
                <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($feed['data'])): ?>
        <div class="empty-state">
            <p>No reviews found in this category yet.</p>
        </div>
    <?php else: ?>
        <div class="review-grid">
            <?php foreach ($feed['data'] as $review): ?>
                <?php require VIEW_PATH . '/reviews/_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?= paginationLinks($feed, url('explore')) ?>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
