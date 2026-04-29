<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="container" style="padding-top:40px; padding-bottom:60px">
    <h1 style="font-family:var(--font-display);font-size:1.8rem;margin-bottom:8px">
        <?= $query ? 'Results for "' . e($query) . '"' : 'Search' ?>
    </h1>

    <!-- Search bar large -->
    <form method="GET" action="<?= url('search') ?>" style="margin-bottom:36px;display:flex;gap:10px;max-width:580px">
        <input type="text" name="q" value="<?= e($query) ?>"
               placeholder="Search products, brands, reviews…"
               style="flex:1;background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;color:var(--text);font-size:1rem">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if ($query && empty($results)): ?>
        <div class="empty-state"><p>No results found for "<?= e($query) ?>".</p></div>

    <?php elseif (!empty($results)): ?>

        <!-- Products section -->
        <?php if (!empty($results['products'])): ?>
            <section style="margin-bottom:48px">
                <h2 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:16px;color:var(--text-dim)">
                    Products (<?= count($results['products']) ?>)
                </h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px">
                    <?php foreach ($results['products'] as $product): ?>
                        <a href="<?= url('products/' . e($product['slug'])) ?>"
                           style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:14px;color:var(--text);display:flex;gap:12px;align-items:center;transition:border-color 0.2s">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= asset($product['image']) ?>" style="width:48px;height:48px;border-radius:6px;object-fit:cover">
                            <?php endif; ?>
                            <div>
                                <strong style="font-size:0.88rem;display:block"><?= e($product['name']) ?></strong>
                                <small style="color:var(--text-muted)"><?= e($product['brand_name']) ?> · <?= $product['review_count'] ?> reviews</small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Reviews section -->
        <?php if (!empty($results['reviews'])): ?>
            <section>
                <h2 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:16px;color:var(--text-dim)">
                    Reviews (<?= count($results['reviews']) ?>)
                </h2>
                <div class="review-grid">
                    <?php foreach ($results['reviews'] as $review): ?>
                        <?php require VIEW_PATH . '/reviews/_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
