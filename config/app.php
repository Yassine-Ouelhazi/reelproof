<?php

// ─── Environment ────────────────────────────────────────────────────────────
define('APP_ENV',      'development'); // 'production' in prod
define('APP_NAME',     'ReelProof');
define('APP_VERSION',  '1.0.0');
define('APP_URL',      'http://localhost/reelproof/public'); // removing /public causes issues with routing
define('APP_DEBUG',    true);

// ─── Database ────────────────────────────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'reelproof');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ─── Session ─────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'reelproof_session');
define('SESSION_LIFETIME', 86400); // 24h

// ─── Upload limits ───────────────────────────────────────────────────────────
define('MAX_VIDEO_SIZE',     524288000); // 500MB
define('MAX_THUMBNAIL_SIZE', 5242880);   // 5MB
define('MAX_AVATAR_SIZE',    2097152);   // 2MB
define('ALLOWED_VIDEO_TYPES',     ['video/mp4', 'video/webm', 'video/quicktime']);
define('ALLOWED_IMAGE_TYPES',     ['image/jpeg', 'image/png', 'image/webp']);

// ─── Pagination ──────────────────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 12);

// ─── Error handling ──────────────────────────────────────────────────────────
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ─── Timezone ────────────────────────────────────────────────────────────────
date_default_timezone_set('Africa/Tunis');
