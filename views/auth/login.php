<?php require VIEW_PATH . '/layouts/header.php'; ?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <a href="<?= url() ?>" class="auth-logo">
                <span class="brand-icon">▶</span> Reel<strong>Proof</strong>
            </a>
            <h1>Welcome back</h1>
            <p>Sign in to your account</p>
        </div>

        <?php $errors = Session::getFlash('errors') ?? []; ?>
        <?php $old    = Session::getFlash('old')    ?? []; ?>

        <?php if (hasFlash('error')): ?>
            <div class="alert alert-error"><?= e(flash('error')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" class="auth-form">
            <?= csrf() ?>

            <div class="form-group <?= !empty($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= e($old['email'] ?? '') ?>"
                       placeholder="you@example.com" required autofocus>
                <?php if (!empty($errors['email'])): ?>
                    <span class="form-error"><?= e($errors['email'][0]) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
                <label for="password">Password</label>
                <div class="input-with-toggle">
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required>
                    <button type="button" class="toggle-password" data-target="password">👁</button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <span class="form-error"><?= e($errors['password'][0]) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>

        <div class="auth-footer">
            <p>No account yet? <a href="<?= url('register') ?>">Create one free</a></p>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.php'; ?>
