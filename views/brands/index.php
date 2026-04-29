<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px;padding-bottom:60px">
    <h1 style="font-family:var(--font-display);font-size:2rem;margin-bottom:32px">Brands</h1>

    <?php if (empty($brands)): ?>
        <div class="empty-state"><p>No brands registered yet.</p></div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px">
            <?php foreach ($brands as $brand): ?>
                <a href="<?= url('brands/' . e($brand['slug'])) ?>"
                   style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;color:var(--text);display:flex;flex-direction:column;gap:12px;transition:all 0.2s">
                    <div style="display:flex;align-items:center;gap:14px">
                        <img src="<?= asset($brand['logo']) ?>" alt="<?= e($brand['name']) ?>"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">
                        <div>
                            <strong style="font-family:var(--font-display);font-size:1rem"><?= e($brand['name']) ?></strong>
                            <?php if (!empty($brand['website'])): ?>
                                <small style="display:block;color:var(--text-muted)"><?= e($brand['website']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;font-size:0.82rem;color:var(--text-muted)">
                        <span>📦 <?= $brand['product_count'] ?> products</span>
                        <span>🎬 <?= $brand['review_count'] ?> reviews</span>
                        <?php if ($brand['avg_rating'] > 0): ?>
                            <span>⭐ <?= round($brand['avg_rating'], 1) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
