-- ReelProof Database Schema
-- Run this in your MySQL client or phpMyAdmin

CREATE DATABASE IF NOT EXISTS reelproof
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE reelproof;

-- ─── Users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username           VARCHAR(30)  NOT NULL UNIQUE,
    email              VARCHAR(180) NOT NULL UNIQUE,
    password           VARCHAR(255) NOT NULL,
    full_name          VARCHAR(80)  DEFAULT '',
    bio                TEXT         DEFAULT NULL,
    avatar             VARCHAR(255) DEFAULT 'uploads/avatars/default.png',
    role               ENUM('reviewer','buyer','brand') NOT NULL DEFAULT 'buyer',
    points             INT UNSIGNED DEFAULT 0,
    credibility_score  TINYINT UNSIGNED DEFAULT 0,  -- 0–100
    is_verified        TINYINT(1) DEFAULT 0,
    created_at         DATETIME NOT NULL,
    updated_at         DATETIME NOT NULL,
    INDEX idx_role (role),
    INDEX idx_credibility (credibility_score DESC)
) ENGINE=InnoDB;

-- ─── Categories ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(80)  NOT NULL,
    slug        VARCHAR(80)  NOT NULL UNIQUE,
    icon        VARCHAR(50)  DEFAULT '📦',
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL
) ENGINE=InnoDB;

-- ─── Brands ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS brands (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,   -- NULL = not registered
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    description TEXT         DEFAULT NULL,
    logo        VARCHAR(255) DEFAULT 'uploads/avatars/default.png',
    website     VARCHAR(255) DEFAULT '',
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ─── Products ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id    INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED DEFAULT NULL,
    name        VARCHAR(120) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    description TEXT         DEFAULT NULL,
    image       VARCHAR(255) DEFAULT '',
    price       DECIMAL(10,2) DEFAULT 0.00,
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    FOREIGN KEY (brand_id)    REFERENCES brands(id)     ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_brand (brand_id),
    INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- ─── Reviews ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    product_id   INT UNSIGNED NOT NULL,
    title        VARCHAR(120) NOT NULL,
    description  TEXT         DEFAULT NULL,
    video_path   VARCHAR(255) NOT NULL,
    thumbnail    VARCHAR(255) DEFAULT NULL,
    rating       TINYINT UNSIGNED NOT NULL DEFAULT 5,  -- 1–5
    upvotes      INT UNSIGNED DEFAULT 0,
    downvotes    INT UNSIGNED DEFAULT 0,
    views        INT UNSIGNED DEFAULT 0,
    status       ENUM('published','pending','rejected') DEFAULT 'published',
    created_at   DATETIME NOT NULL,
    updated_at   DATETIME NOT NULL,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_status   (status),
    INDEX idx_user     (user_id),
    INDEX idx_product  (product_id),
    INDEX idx_upvotes  (upvotes DESC),
    INDEX idx_created  (created_at DESC)
) ENGINE=InnoDB;

-- ─── Review Votes ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS review_votes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    type       ENUM('up','down') NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    UNIQUE KEY unique_vote (review_id, user_id)
) ENGINE=InnoDB;

-- ─── Points Ledger ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS points_ledger (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    points      INT NOT NULL,  -- can be negative
    reason      VARCHAR(120) NOT NULL,
    reference_id INT UNSIGNED DEFAULT NULL,
    created_at  DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- ─── Seed Data ────────────────────────────────────────────────────────────────

INSERT INTO categories (name, slug, icon, created_at, updated_at) VALUES
('Electronics',    'electronics',     '📱', NOW(), NOW()),
('Beauty & Care',  'beauty-care',     '💄', NOW(), NOW()),
('Food & Drinks',  'food-drinks',     '🍕', NOW(), NOW()),
('Home & Living',  'home-living',     '🏠', NOW(), NOW()),
('Fashion',        'fashion',         '👗', NOW(), NOW()),
('Sports',         'sports',          '⚽', NOW(), NOW()),
('Health',         'health',          '💊', NOW(), NOW()),
('Toys & Kids',    'toys-kids',       '🧸', NOW(), NOW());
