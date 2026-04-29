<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="dashboard container">
    <div class="dashboard-header">
        <div class="dashboard-profile">
            <img src="<?= asset($user['avatar'] ?? 'uploads/avatars/default.png') ?>"
                 alt="avatar" class="dashboard-avatar">
            <div>
                <h1>Hey, <?= e($user['username']) ?> 👋</h1>
                <p>Buyer Account</p>
            </div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="<?= url('explore') ?>" class="btn btn-primary">🔍 Explore Reviews</a>
        <a href="<?= url('products') ?>" class="btn btn-ghost">📦 Browse Products</a>
        <a href="<?= url('settings') ?>" class="btn btn-ghost">⚙ Settings</a>
    </div>

    <div style="margin-top:40px;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;text-align:center">
        <div style="font-size:2.5rem;margin-bottom:12px">🛒</div>
        <h2 style="font-family:var(--font-display)">Find your next purchase</h2>
        <p style="color:var(--text-dim);margin:10px 0 20px">Watch honest video reviews from real people before you buy anything.</p>
        <a href="<?= url('explore') ?>" class="btn btn-primary btn-lg">Explore Reviews →</a>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
