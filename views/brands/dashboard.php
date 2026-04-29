<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="dashboard container">
    <div class="dashboard-header">
        <div class="dashboard-profile">
            <img src="<?= asset($brand['logo']) ?>" alt="logo" class="dashboard-avatar">
            <div>
                <h1><?= e($brand['name']) ?> Dashboard</h1>
                <p>Brand Account</p>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><span class="stat-icon">📦</span>
            <div><strong><?= count($products) ?></strong><small>Products</small></div></div>
        <div class="stat-card"><span class="stat-icon">🎬</span>
            <div><strong><?= count($recentReviews) ?>+</strong><small>Reviews</small></div></div>
        <div class="stat-card"><span class="stat-icon">⭐</span>
            <div><strong><?= $brand['avg_rating'] ? round($brand['avg_rating'],1) : '—' ?></strong><small>Avg Rating</small></div></div>
    </div>

    <div class="dashboard-actions">
        <a href="<?= url('brand/products/create') ?>" class="btn btn-primary">+ Add Product</a>
        <a href="<?= url('brands/' . $brand['slug']) ?>" class="btn btn-ghost">View Public Page</a>
        <a href="<?= url('settings') ?>" class="btn btn-ghost">Settings</a>
    </div>

    <!-- Products -->
    <div class="dashboard-section">
        <h2>Your Products</h2>
        <?php if (empty($products)): ?>
            <div class="empty-state"><p>No products yet. <a href="<?= url('brand/products/create') ?>">Add your first product →</a></p></div>
        <?php else: ?>
            <div class="review-list">
                <?php foreach ($products as $p): ?>
                    <div class="review-list-item">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= asset($p['image']) ?>" class="review-list-thumb" alt="">
                        <?php else: ?>
                            <div class="review-list-thumb-placeholder">📦</div>
                        <?php endif; ?>
                        <div class="review-list-info">
                            <strong><?= e($p['name']) ?></strong>
                            <div class="review-list-stats">
                                <span>🎬 <?= $p['review_count'] ?> reviews</span>
                                <?php if ($p['avg_rating'] > 0): ?>
                                    <span>⭐ <?= round($p['avg_rating'],1) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="review-list-actions">
                            <a href="<?= url('products/' . $p['slug']) ?>" class="btn btn-ghost btn-xs">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Reviews -->
    <?php if (!empty($recentReviews)): ?>
    <div class="dashboard-section" style="margin-top:36px">
        <h2>Recent Reviews About Your Products</h2>
        <div class="review-list">
            <?php foreach ($recentReviews as $rev): ?>
                <div class="review-list-item">
                    <div class="review-list-info">
                        <a href="<?= url('reviews/' . $rev['id']) ?>"><strong><?= e($rev['title']) ?></strong></a>
                        <small>by @<?= e($rev['username']) ?> · <?= $rev['rating'] ?>★ · <?= timeAgo($rev['created_at']) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
