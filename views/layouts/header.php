<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ReelProof') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <?php if (isset($extraCss)): ?>
        <link rel="stylesheet" href="<?= asset('css/' . $extraCss . '.css') ?>">
    <?php endif; ?>
</head>
<body>

<nav class="navbar">
    <a href="<?= url() ?>" class="navbar-brand">
        <span class="brand-icon">▶</span>
        <span>Reel<strong>Proof</strong></span>
    </a>

    <div class="navbar-search">
        <form action="<?= url('search') ?>" method="GET">
            <input type="text" name="q" placeholder="Search products, brands, reviews…"
                   value="<?= e($_GET['q'] ?? '') ?>">
            <button type="submit">⌕</button>
        </form>
    </div>

    <div class="navbar-links">
        <a href="<?= url('explore') ?>">Explore</a>
        <a href="<?= url('brands') ?>">Brands</a>
        <a href="<?= url('products') ?>">Products</a>

        <?php if (auth()): ?>
            <?php if (isReviewer()): ?>
                <a href="<?= url('reviews/create') ?>" class="btn btn-post">+ Post Review</a>
            <?php endif; ?>
            <div class="navbar-user">
                <img src="<?= asset(authUser()['avatar'] ?? 'uploads/avatars/default.png') ?>" 
                     alt="avatar" class="navbar-avatar">
                <div class="dropdown">
                    <a href="<?= url('dashboard') ?>">Dashboard</a>
                    <a href="<?= url('profile/' . authUser()['username']) ?>">Profile</a>
                    <a href="<?= url('settings') ?>">Settings</a>
                    <form method="POST" action="<?= url('logout') ?>">
                        <?= csrf() ?>
                        <button type="submit" class="dropdown-logout">Logout</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= url('login') ?>" class="btn btn-ghost">Login</a>
            <a href="<?= url('register') ?>" class="btn btn-primary">Join Free</a>
        <?php endif; ?>
    </div>

    <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
</nav>

<!-- Flash messages -->
<?php if (hasFlash('success')): ?>
    <div class="flash flash-success"><?= e(flash('success')) ?></div>
<?php endif; ?>
<?php if (hasFlash('error')): ?>
    <div class="flash flash-error"><?= e(flash('error')) ?></div>
<?php endif; ?>

<main>
