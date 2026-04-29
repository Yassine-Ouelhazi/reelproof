<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="form-page container">
    <div class="form-card">
        <h1>Edit Review</h1>

        <?php $errors = Session::getFlash('errors') ?? []; ?>

        <form method="POST" action="<?= url('reviews/' . $review['id']) ?>">
            <?= csrf() ?>
            <?= method('POST') ?>

            <div class="form-group <?= !empty($errors['title']) ? 'has-error' : '' ?>">
                <label>Title <span class="req">*</span></label>
                <input type="text" name="title"
                       value="<?= e($review['title']) ?>"
                       required minlength="10" maxlength="120">
                <?php if (!empty($errors['title'])): ?>
                    <span class="form-error"><?= e($errors['title'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Rating <span class="req">*</span></label>
                <div class="star-input" id="starInput">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="star-pick" data-value="<?= $i ?>">★</button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="ratingValue" value="<?= e($review['rating']) ?>">
            </div>

            <div class="form-group <?= !empty($errors['description']) ? 'has-error' : '' ?>">
                <label>Description <span class="req">*</span></label>
                <textarea name="description" rows="5" required minlength="20"><?= e($review['description']) ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <span class="form-error"><?= e($errors['description'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="<?= url('reviews/' . $review['id']) ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
const stars = document.querySelectorAll('.star-pick');
const ratingInput = document.getElementById('ratingValue');
let current = parseInt(ratingInput.value) || 5;

function updateStars(val) {
    stars.forEach((s, i) => s.classList.toggle('selected', i < val));
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
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
