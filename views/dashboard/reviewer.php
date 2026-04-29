<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="dashboard container">
    <div class="dashboard-header">
        <div class="dashboard-profile">
            <img src="<?= asset($user['avatar'] ?? 'uploads/avatars/default.png') ?>" 
                 alt="avatar" class="dashboard-avatar">
            <div>
                <h1>Hey, <?= e($user['username']) ?> 👋</h1>
                <p><?= $user['role'] === 'reviewer' ? 'Reviewer' : 'Buyer' ?> Account</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">🎬</span>
            <div>
                <strong><?= count($reviews) ?></strong>
                <small>Reviews Posted</small>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🔥</span>
            <div>
                <strong><?= $user['credibility_score'] ?>%</strong>
                <small>Credibility Score</small>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🎁</span>
            <div>
                <strong><?= formatNumber((int)$user['points']) ?></strong>
                <small>Points Earned</small>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">👁</span>
            <div>
                <strong><?= formatNumber(array_sum(array_column($reviews, 'views'))) ?></strong>
                <small>Total Views</small>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="dashboard-actions">
        <a href="<?= url('reviews/create') ?>" class="btn btn-primary">+ Post New Review</a>
        <a href="<?= url('profile/' . $user['username']) ?>" class="btn btn-ghost">View Public Profile</a>
        <a href="<?= url('settings') ?>" class="btn btn-ghost">Settings</a>
    </div>

    <!-- Reviews list -->
    <div class="dashboard-section">
        <h2>My Reviews</h2>
        <?php if (empty($reviews)): ?>
            <div class="empty-state">
                <p>No reviews yet. <a href="<?= url('reviews/create') ?>">Post your first review →</a></p>
            </div>
        <?php else: ?>
            <div class="review-list">
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-list-item">
                        <?php if (!empty($rev['thumbnail'])): ?>
                            <img src="<?= asset($rev['thumbnail']) ?>" alt="" class="review-list-thumb">
                        <?php else: ?>
                            <div class="review-list-thumb-placeholder">▶</div>
                        <?php endif; ?>
                        <div class="review-list-info">
                            <a href="<?= url('reviews/' . $rev['id']) ?>">
                                <strong><?= e($rev['title']) ?></strong>
                            </a>
                            <small><?= e($rev['brand_name']) ?> · <?= e($rev['product_name']) ?></small>
                            <div class="review-list-stats">
                                <span>👁 <?= formatNumber((int)$rev['views']) ?></span>
                                <span>👍 <?= $rev['upvotes'] ?></span>
                                <span>👎 <?= $rev['downvotes'] ?></span>
                                <span><?= timeAgo($rev['created_at']) ?></span>
                            </div>
                        </div>
                        <div class="review-list-actions">
                            <a href="<?= url('reviews/' . $rev['id'] . '/edit') ?>" class="btn btn-ghost btn-xs">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
