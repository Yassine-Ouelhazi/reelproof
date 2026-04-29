<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="review-page container-wide">
    <div class="review-main">

        <!-- Video Player -->
        <div class="video-wrapper">
            <video controls preload="metadata" poster="<?= !empty($review['thumbnail']) ? asset($review['thumbnail']) : '' ?>">
                <source src="<?= asset($review['video_path']) ?>" type="video/mp4">
                Your browser does not support video playback.
            </video>
        </div>

        <!-- Review Info -->
        <div class="review-info">
            <div class="review-tags">
                <a href="<?= url('brands/' . e($review['brand_slug'])) ?>" class="tag tag-brand">
                    <?= e($review['brand_name']) ?>
                </a>
                <a href="<?= url('products/' . e($review['product_slug'])) ?>" class="tag tag-product">
                    <?= e($review['product_name']) ?>
                </a>
                <?php if (!empty($review['category_name'])): ?>
                    <span class="tag tag-cat"><?= e($review['category_name']) ?></span>
                <?php endif; ?>
            </div>

            <h1 class="review-title"><?= e($review['title']) ?></h1>

            <div class="review-meta-row">
                <div class="stars-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                    <span class="rating-num"><?= $review['rating'] ?>/5</span>
                </div>
                <span class="review-views">👁 <?= formatNumber((int)$review['views']) ?> views</span>
                <span class="review-time"><?= timeAgo($review['created_at']) ?></span>
            </div>

            <!-- Reviewer -->
            <div class="reviewer-block">
                <img src="<?= asset($review['avatar'] ?? 'uploads/avatars/default.png') ?>" 
                     alt="<?= e($review['username']) ?>" class="reviewer-avatar">
                <div class="reviewer-details">
                    <a href="<?= url('profile/' . e($review['username'])) ?>" class="reviewer-name-lg">
                        @<?= e($review['username']) ?>
                        <?php if (!empty($review['is_verified'])): ?>
                            <span class="verified-badge">✓</span>
                        <?php endif; ?>
                    </a>
                    <div class="credibility-bar" title="Credibility Score: <?= $review['reviewer_score'] ?>%">
                        <div class="credibility-fill" style="width: <?= min(100, $review['reviewer_score']) ?>%"></div>
                        <span>Credibility: <?= $review['reviewer_score'] ?>%</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($review['description'])): ?>
                <div class="review-description">
                    <p><?= nl2br(e($review['description'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Vote buttons -->
            <div class="vote-section" data-review-id="<?= $review['id'] ?>">
                <button class="vote-btn vote-up <?= $userVote === 'up' ? 'voted' : '' ?>"
                        data-type="up" <?= !auth() ? 'disabled title="Login to vote"' : '' ?>>
                    👍 <span class="vote-count"><?= $review['upvotes'] ?></span>
                </button>
                <button class="vote-btn vote-down <?= $userVote === 'down' ? 'voted' : '' ?>"
                        data-type="down" <?= !auth() ? 'disabled title="Login to vote"' : '' ?>>
                    👎 <span class="vote-count"><?= $review['downvotes'] ?></span>
                </button>
                <?php if (!auth()): ?>
                    <a href="<?= url('login') ?>" class="vote-login-cta">Login to vote</a>
                <?php endif; ?>
            </div>

            <!-- Owner actions -->
            <?php if (auth() && Session::get('user_id') == $review['user_id']): ?>
                <div class="owner-actions">
                    <a href="<?= url('reviews/' . $review['id'] . '/edit') ?>" class="btn btn-ghost btn-sm">Edit</a>
                    <form method="POST" action="<?= url('reviews/' . $review['id']) ?>" 
                          onsubmit="return confirm('Delete this review?')">
                        <?= csrf() ?>
                        <?= method('DELETE') ?>
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar: Related -->
    <?php if (!empty($related)): ?>
        <aside class="review-sidebar">
            <h3>More reviews of <?= e($review['product_name']) ?></h3>
            <?php foreach ($related as $rel): ?>
                <a href="<?= url('reviews/' . $rel['id']) ?>" class="related-card">
                    <?php if (!empty($rel['thumbnail'])): ?>
                        <img src="<?= asset($rel['thumbnail']) ?>" alt="<?= e($rel['title']) ?>">
                    <?php else: ?>
                        <div class="related-placeholder">▶</div>
                    <?php endif; ?>
                    <div class="related-info">
                        <span><?= e(truncate($rel['title'], 60)) ?></span>
                        <small>@<?= e($rel['username']) ?> · <?= $rel['rating'] ?>★</small>
                    </div>
                </a>
            <?php endforeach; ?>
        </aside>
    <?php endif; ?>
</div>

<script>
// Vote system
const voteSection = document.querySelector('.vote-section');
if (voteSection) {
    const reviewId = voteSection.dataset.reviewId;
    voteSection.querySelectorAll('.vote-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const type = btn.dataset.type;
            try {
                const res = await fetch(`<?= url('reviews/') ?>${reviewId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `type=${type}&_csrf_token=<?= Session::csrfToken() ?>`
                });
                const data = await res.json();
                if (!data.error) {
                    voteSection.querySelector('.vote-up .vote-count').textContent  = data.upvotes;
                    voteSection.querySelector('.vote-down .vote-count').textContent = data.downvotes;

                    voteSection.querySelectorAll('.vote-btn').forEach(b => b.classList.remove('voted'));
                    if (data.action === 'added' || data.action === 'switched') {
                        voteSection.querySelector(`.vote-${data.type}`).classList.add('voted');
                    }
                }
            } catch (e) {
                console.error('Vote failed', e);
            }
        });
    });
}
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
