<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="auth-page">
    <div class="auth-card auth-card-wide">
        <div class="auth-header">
            <a href="<?= url() ?>" class="auth-logo">
                <span class="brand-icon">▶</span> Reel<strong>Proof</strong>
            </a>
            <h1>Join ReelProof</h1>
            <p>Free forever. No hidden costs.</p>
        </div>

        <?php $errors = Session::getFlash('errors') ?? []; ?>
        <?php $old    = Session::getFlash('old')    ?? []; ?>

        <?php if (hasFlash('error')): ?>
            <div class="alert alert-error"><?= e(flash('error')) ?></div>
        <?php endif; ?>

        <!-- Role selector -->
        <div class="role-selector">
            <button type="button" class="role-btn active" data-role="reviewer">
                <span class="role-icon">🎬</span>
                <strong>Reviewer</strong>
                <small>Post video reviews & earn rewards</small>
            </button>
            <button type="button" class="role-btn" data-role="buyer">
                <span class="role-icon">🛒</span>
                <strong>Buyer</strong>
                <small>Watch reviews & buy with confidence</small>
            </button>
            <button type="button" class="role-btn" data-role="brand">
                <span class="role-icon">🏢</span>
                <strong>Brand</strong>
                <small>Get honest exposure for your products</small>
            </button>
        </div>

        <form method="POST" action="<?= url('register') ?>" class="auth-form">
            <?= csrf() ?>
            <input type="hidden" name="role" id="selectedRole" value="<?= e($old['role'] ?? 'reviewer') ?>">

            <div class="form-row">
                <div class="form-group <?= !empty($errors['username']) ? 'has-error' : '' ?>">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           value="<?= e($old['username'] ?? '') ?>"
                           placeholder="coolreviewer99" required>
                    <?php if (!empty($errors['username'])): ?>
                        <span class="form-error"><?= e($errors['username'][0]) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= !empty($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= e($old['email'] ?? '') ?>"
                           placeholder="you@example.com" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="form-error"><?= e($errors['email'][0]) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Brand name field (shown only for brand role) -->
            <div class="form-group brand-only hidden">
                <label for="brand_name">Brand / Company Name</label>
                <input type="text" id="brand_name" name="brand_name"
                       value="<?= e($old['brand_name'] ?? '') ?>"
                       placeholder="Your brand name">
            </div>

            <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
                <label for="password">Password</label>
                <div class="input-with-toggle">
                    <input type="password" id="password" name="password"
                           placeholder="Min 8 characters" required minlength="8">
                    <button type="button" class="toggle-password" data-target="password">👁</button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <span class="form-error"><?= e($errors['password'][0]) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Create Account</button>

            <p class="auth-terms">By joining, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</p>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="<?= url('login') ?>">Sign in</a></p>
        </div>
    </div>
</div>

<script>
// Role selector logic
document.querySelectorAll('.role-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('selectedRole').value = btn.dataset.role;
        // Show/hide brand name field
        const brandField = document.querySelector('.brand-only');
        if (btn.dataset.role === 'brand') {
            brandField.classList.remove('hidden');
        } else {
            brandField.classList.add('hidden');
        }
    });
});

// Pre-select role if old value exists
const oldRole = '<?= e($old['role'] ?? 'reviewer') ?>';
document.querySelector(`[data-role="${oldRole}"]`)?.click();
</script>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
