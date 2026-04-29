<?php

// ── Public Routes ─────────────────────────────────────────────────────────────
$router->get('/',                  'HomeController@index');
$router->get('/explore',           'HomeController@explore');
$router->get('/search',            'HomeController@search');

// ── Auth Routes ───────────────────────────────────────────────────────────────
$router->get('/login',             'AuthController@loginForm',    ['GuestMiddleware']);
$router->post('/login',            'AuthController@login',        ['GuestMiddleware']);
$router->get('/register',          'AuthController@registerForm', ['GuestMiddleware']);
$router->post('/register',         'AuthController@register',     ['GuestMiddleware']);
$router->post('/logout',           'AuthController@logout',       ['AuthMiddleware']);

// ── Review Routes ─────────────────────────────────────────────────────────────
$router->get('/reviews',           'ReviewController@index');
$router->get('/reviews/create',    'ReviewController@create',     ['AuthMiddleware']);
$router->post('/reviews',          'ReviewController@store',      ['AuthMiddleware']);
$router->get('/reviews/{id}',      'ReviewController@show');
$router->get('/reviews/{id}/edit', 'ReviewController@edit',       ['AuthMiddleware']);
$router->post('/reviews/{id}',     'ReviewController@update',     ['AuthMiddleware']);
$router->delete('/reviews/{id}',   'ReviewController@destroy',    ['AuthMiddleware']);

// ── Vote on reviews ───────────────────────────────────────────────────────────
$router->post('/reviews/{id}/vote', 'ReviewController@vote',      ['AuthMiddleware']);

// ── Product Routes ────────────────────────────────────────────────────────────
$router->get('/products',          'ProductController@index');
$router->get('/products/{slug}',   'ProductController@show');

// ── Brand Routes ──────────────────────────────────────────────────────────────
$router->get('/brands',            'BrandController@index');
$router->get('/brands/{slug}',     'BrandController@show');

// ── Category Routes ───────────────────────────────────────────────────────────
$router->get('/category/{slug}',   'HomeController@category');

// ── Dashboard (shared, role-aware) ────────────────────────────────────────────
$router->get('/dashboard',         'DashboardController@index',   ['AuthMiddleware']);

// ── Profile Routes ────────────────────────────────────────────────────────────
$router->get('/profile/{username}', 'ProfileController@show');
$router->get('/settings',           'ProfileController@settings', ['AuthMiddleware']);
$router->post('/settings',          'ProfileController@update',   ['AuthMiddleware']);

// ── Brand Dashboard ───────────────────────────────────────────────────────────
$router->get('/brand/dashboard',    'BrandController@dashboard',  ['AuthMiddleware']);
$router->get('/brand/products/create', 'BrandController@createProduct', ['AuthMiddleware']);
$router->post('/brand/products',    'BrandController@storeProduct', ['AuthMiddleware']);
