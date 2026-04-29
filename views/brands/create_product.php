<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="form-page container">
    <div class="form-card">
        <h1>Add New Product</h1>
        <p class="form-subtitle">Add a product so reviewers can post honest video reviews about it.</p>

        <?php $errors = Session::getFlash('errors') ?? []; ?>

        <form method="POST" action="<?= url('brand/products') ?>" enctype="multipart/form-data">
            <?= csrf() ?>

            <div class="form-group <?= !empty($errors['name']) ? 'has-error' : '' ?>">
                <label>Product Name <span class="req">*</span></label>
                <input type="text" name="name" placeholder="e.g. ProMax Wireless Headphones" required>
                <?php if (!empty($errors['name'])): ?>
                    <span class="form-error"><?= e($errors['name'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= !empty($errors['category_id']) ? 'has-error' : '' ?>">
                <label>Category <span class="req">*</span></label>
                <select name="category_id" required>
                    <option value="">— Select category —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['icon']) ?> <?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Price (optional)</label>
                <input type="number" name="price" placeholder="0.00" step="0.01" min="0">
            </div>

            <div class="form-group <?= !empty($errors['description']) ? 'has-error' : '' ?>">
                <label>Product Description <span class="req">*</span></label>
                <textarea name="description" rows="4" placeholder="Describe what this product is and who it's for…" required></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <span class="form-error"><?= e($errors['description'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Product Image</label>
                <div class="upload-zone upload-zone-sm">
                    <input type="file" name="image" accept="image/*">
                    <div class="upload-zone-inner">
                        <span class="upload-icon">🖼</span>
                        <strong>Upload product image</strong>
                        <small>JPG, PNG, WebP — max 5MB</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= url('brand/dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Product →</button>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
