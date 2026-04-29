<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container-wide" style="padding-top:40px;padding-bottom:60px">
    <div style="margin-bottom:28px">
        <span style="font-size:2rem"><?= e($category['icon']) ?></span>
        <h1 style="font-family:var(--font-display);font-size:2rem;margin-top:8px"><?= e($category['name']) ?></h1>
    </div>

    <?php if (empty($feed['data'])): ?>
        <div class="empty-state"><p>No reviews in this category yet.</p></div>
    <?php else: ?>
        <div class="review-grid">
            <?php foreach ($feed['data'] as $review): ?>
                <?php require VIEW_PATH . '/reviews/_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?= paginationLinks($feed, url('category/' . $category['slug'])) ?>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
