<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px;padding-bottom:60px">
    <!-- Profile header -->
    <div style="display:flex;gap:24px;align-items:flex-start;margin-bottom:40px;flex-wrap:wrap">
        <img src="<?= asset($profile['avatar'] ?? 'uploads/avatars/default.png') ?>"
             alt="<?= e($profile['username']) ?>"
             style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--accent)">
        <div>
            <div style="display:flex;align-items:center;gap:10px">
                <h1 style="font-family:var(--font-display);font-size:1.8rem">@<?= e($profile['username']) ?></h1>
                <?php if (!empty($profile['is_verified'])): ?>
                    <span class="verified-badge" style="width:22px;height:22px;font-size:0.85rem">✓</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($profile['full_name'])): ?>
                <p style="color:var(--text-dim);margin-top:4px"><?= e($profile['full_name']) ?></p>
            <?php endif; ?>
            <?php if (!empty($profile['bio'])): ?>
                <p style="color:var(--text-dim);margin-top:8px;max-width:480px"><?= e($profile['bio']) ?></p>
            <?php endif; ?>
            <div style="display:flex;gap:20px;margin-top:12px;font-size:0.875rem;color:var(--text-muted)">
                <span>🎬 <?= count($reviews) ?> reviews</span>
                <?php if ($profile['role'] === 'reviewer'): ?>
                    <span>🔥 <?= $profile['credibility_score'] ?>% credibility</span>
                    <span>🎁 <?= formatNumber((int)$profile['points']) ?> points</span>
                <?php endif; ?>
                <span>📅 Joined <?= date('M Y', strtotime($profile['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Reviews grid -->
    <?php if ($profile['role'] === 'reviewer'): ?>
        <h2 style="font-family:var(--font-display);font-size:1.3rem;margin-bottom:20px">Reviews by @<?= e($profile['username']) ?></h2>
        <?php if (empty($reviews)): ?>
            <div class="empty-state"><p>No reviews posted yet.</p></div>
        <?php else: ?>
            <div class="review-grid">
                <?php foreach ($reviews as $review): ?>
                    <?php require VIEW_PATH . '/reviews/_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
