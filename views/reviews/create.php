<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="form-page container">
    <div class="form-card">
        <h1>Post Your Review</h1>
        <p class="form-subtitle">Share your honest experience. Upload a video, rate the product, earn points.</p>

        <?php $errors = Session::getFlash('errors') ?? []; ?>
        <?php $old    = Session::getFlash('old')    ?? []; ?>

        <form method="POST" action="<?= url('reviews') ?>" enctype="multipart/form-data" class="review-form">
            <?= csrf() ?>

            <!-- Product Selection -->
            <div class="form-group <?= !empty($errors['product_id']) ? 'has-error' : '' ?>">
                <label for="product_id">Product Being Reviewed <span class="req">*</span></label>
                <select name="product_id" id="product_id" required>
                    <option value="">— Select a product —</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>"
                            <?= ($old['product_id'] ?? '') == $product['id'] ? 'selected' : '' ?>>
                            <?= e($product['brand_name']) ?> — <?= e($product['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['product_id'])): ?>
                    <span class="form-error"><?= e($errors['product_id'][0]) ?></span>
                <?php endif; ?>
            </div>

            <!-- Title -->
            <div class="form-group <?= !empty($errors['title']) ? 'has-error' : '' ?>">
                <label for="title">Review Title <span class="req">*</span></label>
                <input type="text" id="title" name="title"
                       value="<?= e($old['title'] ?? '') ?>"
                       placeholder="e.g. I used this for 3 months — honest verdict"
                       required minlength="10" maxlength="120">
                <?php if (!empty($errors['title'])): ?>
                    <span class="form-error"><?= e($errors['title'][0]) ?></span>
                <?php endif; ?>
            </div>

            <!-- Rating -->
            <div class="form-group <?= !empty($errors['rating']) ? 'has-error' : '' ?>">
                <label>Rating <span class="req">*</span></label>
                <div class="star-input" id="starInput">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="star-pick" data-value="<?= $i ?>">★</button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="ratingValue" value="<?= e($old['rating'] ?? '5') ?>">
                <?php if (!empty($errors['rating'])): ?>
                    <span class="form-error"><?= e($errors['rating'][0]) ?></span>
                <?php endif; ?>
            </div>

            <!-- Video Upload -->
            <div class="form-group <?= !empty($errors['video']) ? 'has-error' : '' ?>">
                <label for="video">Review Video <span class="req">*</span></label>
                <div class="upload-zone" id="videoUploadZone">
                    <input type="file" id="video" name="video" accept="video/*" required>
                    <div class="upload-zone-inner">
                        <span class="upload-icon">🎬</span>
                        <strong>Upload your video</strong>
                        <small>MP4, WebM or MOV — max 500MB</small>
                    </div>
                    <div class="upload-preview" id="videoPreview" style="display:none">
                        <video id="videoPreviewEl" controls></video>
                        <button type="button" class="remove-file" id="removeVideo">✕</button>
                    </div>
                </div>
                <?php if (!empty($errors['video'])): ?>
                    <span class="form-error"><?= e($errors['video'][0]) ?></span>
                <?php endif; ?>
            </div>

            <!-- Thumbnail -->
            <div class="form-group">
                <label for="thumbnail">Thumbnail Image <small>(optional)</small></label>
                <div class="upload-zone upload-zone-sm">
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                    <div class="upload-zone-inner">
                        <span class="upload-icon">🖼</span>
                        <strong>Upload thumbnail</strong>
                        <small>JPG, PNG, WebP — max 5MB</small>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group <?= !empty($errors['description']) ? 'has-error' : '' ?>">
                <label for="description">Description <span class="req">*</span></label>
                <textarea id="description" name="description" rows="5"
                          placeholder="Tell people what you loved, what disappointed you, and who should or shouldn't buy this product…"
                          required minlength="20"><?= e($old['description'] ?? '') ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <span class="form-error"><?= e($errors['description'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="<?= url('dashboard') ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Post Review →</button>
            </div>
        </form>
    </div>
</div>

<script>
// Star rating
const stars   = document.querySelectorAll('.star-pick');
const ratingInput = document.getElementById('ratingValue');
let   current = parseInt(ratingInput.value) || 5;

function updateStars(val) {
    stars.forEach((s, i) => {
        s.classList.toggle('selected', i < val);
    });
}
updateStars(current);

stars.forEach(star => {
    star.addEventListener('mouseover', () => updateStars(parseInt(star.dataset.value)));
    star.addEventListener('mouseleave', () => updateStars(current));
    star.addEventListener('click', () => {
        current = parseInt(star.dataset.value);
        ratingInput.value = current;
        updateStars(current);
    });
});

// Video preview
const videoInput = document.getElementById('video');
const videoPreview = document.getElementById('videoPreview');
const videoPreviewEl = document.getElementById('videoPreviewEl');

videoInput.addEventListener('change', () => {
    const file = videoInput.files[0];
    if (file) {
        videoPreviewEl.src = URL.createObjectURL(file);
        videoPreview.style.display = 'block';
        document.querySelector('#videoUploadZone .upload-zone-inner').style.display = 'none';
    }
});

document.getElementById('removeVideo')?.addEventListener('click', () => {
    videoInput.value = '';
    videoPreviewEl.src = '';
    videoPreview.style.display = 'none';
    document.querySelector('#videoUploadZone .upload-zone-inner').style.display = 'flex';
});
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
