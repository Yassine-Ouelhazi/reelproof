# Database Configuration Guide for ReelProof v0.0.0

## Current Configuration Status ✅

Your `config/app.php` is **already correctly configured** for local XAMPP development.

---

## Database Connection Settings

```php
// These are in config/app.php - READY TO USE
define('DB_HOST',    'localhost');   // MySQL server address
define('DB_NAME',    'reelproof');   // Database name
define('DB_USER',    'root');        // MySQL username
define('DB_PASS',    '');            // MySQL password (empty for XAMPP)
define('DB_CHARSET', 'utf8mb4');     // Character encoding
```

### What These Mean:

| Setting      | Current Value | Purpose                                    |
| ------------ | ------------- | ------------------------------------------ |
| `DB_HOST`    | `localhost`   | MySQL server location (local machine)      |
| `DB_NAME`    | `reelproof`   | Database name you created                  |
| `DB_USER`    | `root`        | Default XAMPP MySQL username               |
| `DB_PASS`    | `` (empty)    | Default XAMPP MySQL password (no password) |
| `DB_CHARSET` | `utf8mb4`     | Unicode support for multiple languages     |

---

## Application Settings

```php
// Environment & Debug
define('APP_ENV',      'development');  // Use 'production' when live
define('APP_NAME',     'ReelProof');    // Your app name
define('APP_VERSION',  '1.0.0');        // Version number
define('APP_URL',      'http://localhost/reelproof/public');  // ← YOUR APP URL
define('APP_DEBUG',    true);           // Show errors (set false in production)
```

### APP_URL Breakdown

```
http://localhost/reelproof/public
│       │        │                  │
│       │        │                  └─ public folder (web root)
│       │        └───────────────────── project folder in htdocs
│       └────────────────────────────── local machine
└──────────────────────────────────────── protocol (HTTP)
```

---

## How the Database Connection Works

### Connection Flow

1. **Bootstrap** (`public/index.php`)
   - Loads `config/app.php` with database constants

2. **Database Singleton** (`app/Core/Database.php`)
   - Creates single PDO connection using constants
   - Uses these credentials to connect to MySQL

3. **DSN String** (Automatically built)

   ```php
   mysql:host=localhost;dbname=reelproof;charset=utf8mb4
   ```

4. **PDO Connection**
   ```php
   new PDO(
       'mysql:host=localhost;dbname=reelproof;charset=utf8mb4',
       'root',    // User
       '',        // Password (empty)
       [/* options */]
   );
   ```

---

## Troubleshooting Database Connection

### Problem: "Database connection failed"

**Step 1: Verify MySQL is Running**

- Open XAMPP Control Panel
- Check MySQL module shows "Running" (green)
- If not, click "Start"

**Step 2: Verify Database Exists**

```bash
# Via MySQL CLI
mysql -u root -p

# (Press Enter for empty password)
```

```sql
-- Check if database exists
SHOW DATABASES;
-- Should show 'reelproof' in the list
```

**Step 3: Verify Tables Exist**

```sql
USE reelproof;
SHOW TABLES;
-- Should display: users, categories, brands, products, reviews, etc.
```

**Step 4: Check Config File**

- Ensure `config/app.php` has correct values
- No typos in `DB_NAME` or `DB_HOST`

**Step 5: Check Error Log**

```
xampp/php/logs/php_error_log
```

---

## Database Setup Checklist

If you're setting up the database from scratch:

### Via phpMyAdmin (Easiest)

- [ ] Open `http://localhost/phpmyadmin`
- [ ] Click "New" in left panel
- [ ] Database name: `reelproof`
- [ ] Collation: `utf8mb4_unicode_ci`
- [ ] Click "Create"
- [ ] Click "Import" tab
- [ ] Select `database/migrations/001_create_tables.sql`
- [ ] Click "Go"

### Via MySQL CLI

```bash
# 1. Login to MySQL
mysql -u root

# 2. Run migration file
source C:/xampp/htdocs/reelproof/database/migrations/001_create_tables.sql

# 3. Verify setup
USE reelproof;
SHOW TABLES;
```

---

## Session Configuration

```php
define('SESSION_NAME',     'reelproof_session');  // Session cookie name
define('SESSION_LIFETIME', 86400);               // 24 hours in seconds
```

- Sessions store user login information
- Auto-cleared after 24 hours of inactivity
- Stored in server-side PHP temp directory

---

## Upload & Content Configuration

```php
define('MAX_VIDEO_SIZE',       524288000);  // 500MB max video
define('MAX_THUMBNAIL_SIZE',   5242880);    // 5MB max thumbnail
define('MAX_AVATAR_SIZE',      2097152);    // 2MB max profile pic

define('ALLOWED_VIDEO_TYPES', [
    'video/mp4',
    'video/webm',
    'video/quicktime'
]);

define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/webp'
]);

define('ITEMS_PER_PAGE', 12);  // Pagination items per page
```

---

## Timezone Configuration

```php
date_default_timezone_set('Africa/Tunis');
```

All timestamps in database use this timezone. To change:

- Find valid timezone at [PHP Timezone List](https://www.php.net/manual/en/timezones.php)
- Examples: `'America/New_York'`, `'Europe/London'`, `'Asia/Tokyo'`

---

## Production Deployment Changes

When deploying to production, change `config/app.php`:

```php
// BEFORE (Development)
define('APP_ENV',      'development');
define('APP_DEBUG',    true);
define('APP_URL',      'http://localhost/reelproof/public');

// AFTER (Production)
define('APP_ENV',      'production');
define('APP_DEBUG',    false);
define('APP_URL',      'https://yourdomain.com');

// Update database credentials
define('DB_HOST',     'your-db-server.com');
define('DB_USER',     'prod_user');
define('DB_PASS',     'strong_password_here');
```

---

## Testing the Configuration

### Test 1: PHP Connection

Create file `test_db.php` in `public/`:

```php
<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

try {
    $db = Database::getInstance();
    $result = $db->query('SELECT 1');
    echo "✅ Database connection successful!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

Visit: `http://localhost/reelproof/public/test_db.php`

### Test 2: Model Query

In a controller:

```php
$users = (new UserModel())->all();
echo "Users in database: " . count($users);
```

---

## Configuration Constants Used by Framework

| Constant                                   | Used By          | Purpose                 |
| ------------------------------------------ | ---------------- | ----------------------- |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | `Database.php`   | MySQL connection        |
| `APP_DEBUG`                                | `config/app.php` | Error display level     |
| `VIEW_PATH`                                | `Controller.php` | Template directory      |
| `APP_URL`                                  | Views            | Form actions, redirects |
| `SESSION_LIFETIME`                         | `Session.php`    | Login timeout           |
| `ITEMS_PER_PAGE`                           | `Model.php`      | Pagination default      |

---

## Security Notes

### Development (Current Setup)

✅ Empty database password is fine for local testing  
✅ `APP_DEBUG=true` helps during development  
✅ HTTP (not HTTPS) is acceptable locally

### Production Deployment

⚠️ **NEVER use empty passwords in production**  
⚠️ **NEVER expose debug information**  
⚠️ **ALWAYS use HTTPS**  
⚠️ **Use strong, unique database passwords**  
⚠️ **Restrict database user permissions**

---

## Quick Reference

```php
// What you need to change for different environments:

// LOCAL DEVELOPMENT (current - XAMPP)
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('APP_DEBUG', true);

// STAGING SERVER
define('DB_HOST',   'staging-db.company.com');
define('DB_USER',   'app_user');
define('DB_PASS',   'staging_password');
define('APP_DEBUG', false);

// PRODUCTION SERVER
define('DB_HOST',   'prod-db.company.com');
define('DB_USER',   'prod_app_user');
define('DB_PASS',   'very_strong_password');
define('APP_DEBUG', false);
```

---

## Your Current Setup Summary

✅ **MySQL Server:** `localhost` (XAMPP)  
✅ **Database:** `reelproof`  
✅ **Username:** `root` (XAMPP default)  
✅ **Password:** Empty (XAMPP default)  
✅ **Charset:** `utf8mb4` (Unicode)  
✅ **App URL:** `http://localhost/reelproof/public`  
✅ **Debug Mode:** Enabled (development)

**Status:** 🟢 **Ready to use immediately!**

---

**Last Updated:** April 29, 2026
