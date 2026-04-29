<article class="review-card">
    <a href="<?= url('reviews/' . $review['id']) ?>" class="review-card-video">
        <?php if (!empty($review['thumbnail'])): ?>
            <img src="<?= asset($review['thumbnail']) ?>" alt="<?= e($review['title']) ?>" loading="lazy">
        <?php else: ?>
            <div class="review-card-placeholder">
                <span>▶</span>
            </div>
        <?php endif; ?>
        <div class="review-card-overlay">
            <span class="play-btn">▶</span>
        </div>
        <div class="review-card-rating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
            <?php endfor; ?>
        </div>
    </a>

    <div class="review-card-body">
        <div class="review-card-meta">
            <a href="<?= url('products/' . e($review['product_slug'])) ?>" class="review-product-tag">
                <?= e($review['brand_name']) ?> · <?= e($review['product_name']) ?>
            </a>
        </div>

        <h3 class="review-card-title">
            <a href="<?= url('reviews/' . $review['id']) ?>"><?= e($review['title']) ?></a>
        </h3>

        <div class="review-card-footer">
            <a href="<?= url('profile/' . e($review['username'])) ?>" class="reviewer-info">
                <img src="<?= asset($review['avatar'] ?? 'uploads/avatars/default.png') ?>" 
                     alt="<?= e($review['username']) ?>" class="reviewer-avatar-sm">
                <span class="reviewer-name">@<?= e($review['username']) ?></span>
                <?php if (!empty($review['is_verified'])): ?>
                    <span class="verified-badge" title="Verified Reviewer">✓</span>
                <?php endif; ?>
            </a>

            <div class="review-card-stats">
                <span title="Credibility">🔥 <?= e($review['reviewer_score']) ?>%</span>
                <span title="Views">👁 <?= formatNumber((int)$review['views']) ?></span>
                <span title="Upvotes">👍 <?= formatNumber((int)$review['upvotes']) ?></span>
            </div>
        </div>

        <div class="review-card-time"><?= timeAgo($review['created_at']) ?></div>
    </div>
</article>
