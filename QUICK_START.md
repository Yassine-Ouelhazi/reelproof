# ReelProof Quick Start Guide

## ✅ Status: Ready to Run!

Your project is **fully configured and ready to use**. No changes needed to `config/app.php`!

---

## 📋 Pre-Requisites Check

- [ ] XAMPP installed
- [ ] PHP 8.1+ available
- [ ] MySQL 8.0+ available
- [ ] Apache mod_rewrite enabled

---

## 🚀 Getting Started (5 Steps)

### Step 1: Start Services

1. Open **XAMPP Control Panel**
2. Click "Start" for:
   - ✅ Apache
   - ✅ MySQL
3. Wait for both to show green "Running" status

### Step 2: Create Database

**Option A: phpMyAdmin (Recommended)**

1. Open browser: `http://localhost/phpmyadmin`
2. Left panel → Click "New"
3. Database name: `reelproof`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Click "Import" tab
7. Choose file: `database/migrations/001_create_tables.sql`
8. Click "Go"

**Option B: MySQL CLI**

```bash
mysql -u root -p < D:\xampp\htdocs\reelproof\database\migrations\001_create_tables.sql
# Press Enter when prompted for password (empty for XAMPP)
```

### Step 3: Verify Configuration

Check `config/app.php`:

```php
define('DB_HOST',   'localhost');     ✅ localhost
define('DB_NAME',   'reelproof');     ✅ reelproof
define('DB_USER',   'root');          ✅ root
define('DB_PASS',   '');              ✅ empty (XAMPP default)
define('APP_URL',   'http://localhost/reelproof/public');  ✅ correct URL
```

**If these are different, update them now.**

### Step 4: Open Application

Open browser and navigate to:

```
http://localhost/reelproof/public
```

✅ You should see the ReelProof homepage!

### Step 5: Create First Account

1. Click "Register" (or "Sign Up")
2. Fill in form:
   - Username: `john_doe`
   - Email: `john@example.com`
   - Password: `password123`
   - Role: Choose one
3. Click "Register"
4. Login with your credentials
5. Explore the application!

---

## 🗄️ Database Connection Details

| Setting      | Value                      |
| ------------ | -------------------------- |
| **Server**   | `localhost` (your machine) |
| **Database** | `reelproof`                |
| **Username** | `root`                     |
| **Password** | (empty)                    |
| **Port**     | `3306` (MySQL default)     |

### Verify Database is Set Up

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Left side, expand `reelproof` database
3. You should see these tables:
   - `users`
   - `categories`
   - `brands`
   - `products`
   - `reviews`
   - `review_votes`

If tables don't appear → Re-run migration (Step 2)

---

## 📂 Project Structure Quick Reference

```
reelproof/
├── app/                    # PHP code
│   ├── Controllers/        # Handle requests
│   ├── Models/            # Database queries
│   ├── Core/              # Framework code
│   └── Middleware/        # Filters (auth, etc.)
│
├── config/
│   └── app.php            # ← All config settings here
│
├── public/
│   └── index.php          # ← Entry point
│
├── routes/
│   └── web.php            # URL routes
│
├── views/                 # HTML templates
│   ├── auth/              # Login/register
│   ├── home/              # Homepage
│   ├── reviews/           # Review pages
│   └── ...
│
├── database/
│   └── migrations/
│       └── 001_create_tables.sql  # Database schema
│
└── Documentation files
    ├── PROJECT_DOCUMENTATION.md
    ├── DATABASE_CONFIG_GUIDE.md
    └── ARCHITECTURE.md
```

---

## 🔌 Connection String (What Happens Behind Scenes)

When you start the app:

```
1. config/app.php loads with constants
2. app/Core/Database.php creates connection:

   DSN = "mysql:host=localhost;dbname=reelproof;charset=utf8mb4"
   User = "root"
   Password = ""

3. PDO creates connection to MySQL
4. App uses this connection for all queries
```

---

## ⚙️ Configuration Explained

### What Each Setting Does

```php
// Environment
define('APP_ENV', 'development');
// Use 'production' when deployed

define('APP_DEBUG', true);
// Shows detailed errors (set false in production!)

define('APP_URL', 'http://localhost/reelproof/public');
// Used for links, redirects, forms in views

// Database
define('DB_HOST', 'localhost');       // MySQL server
define('DB_NAME', 'reelproof');       // Database name
define('DB_USER', 'root');            // MySQL username
define('DB_PASS', '');                // MySQL password
define('DB_CHARSET', 'utf8mb4');      // Character encoding

// Session
define('SESSION_LIFETIME', 86400);    // 24 hours login timeout

// Uploads
define('MAX_VIDEO_SIZE', 524288000);  // 500MB max video
```

---

## 🧪 Test the Connection

Create a test file to verify database works:

### Method 1: Create Test File

Create `public/test_connection.php`:

```php
<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';

echo "<h1>ReelProof Database Test</h1>";

try {
    $db = Database::getInstance();
    echo "✅ <strong>Database connection successful!</strong>";

    // Test query
    $result = $db->query("SELECT COUNT(*) as users FROM users");
    $count = $result->fetch();
    echo "<br>Users in database: " . $count['users'];

} catch (Exception $e) {
    echo "❌ <strong>Connection failed:</strong><br>";
    echo $e->getMessage();
}
?>
```

Visit: `http://localhost/reelproof/public/test_connection.php`

### Method 2: Check in Browser Console

After registering a user:

1. Visit `http://localhost/phpmyadmin`
2. Select `reelproof` database
3. Click `users` table
4. You should see your registered user

---

## 🎯 Common Tasks

### Create a New Controller

1. Create file: `app/Controllers/MyController.php`
2. Extend base class:

```php
<?php

class MyController extends Controller
{
    public function index(Request $request): void
    {
        // Your logic here
        $this->view('my.index', ['data' => ...]);
    }
}
```

3. Add route in `routes/web.php`:

```php
$router->get('/my-route', 'MyController@index');
```

### Create a New Model

1. Create file: `app/Models/MyModel.php`
2. Extend base class:

```php
<?php

class MyModel extends Model
{
    protected string $table = 'my_table_name';

    // Add custom queries if needed
    public function custom(): array
    {
        // Your query
    }
}
```

### Use Model in Controller

```php
class MyController extends Controller
{
    public function show(Request $request): void
    {
        $model = new MyModel();
        $item = $model->find(1);
        $this->view('my.show', ['item' => $item]);
    }
}
```

### Create a New View

1. Create file: `views/my/index.php`
2. Use passed variables:

```php
<h1><?php echo $title; ?></h1>
<p><?php echo $description; ?></p>
```

3. Render from controller:

```php
$this->view('my.index', [
    'title' => 'My Page',
    'description' => 'Page description'
]);
```

---

## 🔐 Authentication

### Check if User is Logged In

```php
// In Controller
if ($this->isAuthenticated()) {
    $user = $this->currentUser();
    echo "Logged in as: " . $user['username'];
}
```

### Require Login

```php
// In Controller method
$this->requireAuth();  // Redirects to login if not authenticated

// In Route definition
$router->get('/my-page', 'MyController@page', ['AuthMiddleware']);
```

### Get Current User

```php
$user = $this->currentUser();
// Returns array with id, username, email, role, etc.
```

### Check User Role

```php
$this->requireRole('brand');  // Only 'brand' users can access
```

---

## 📊 Database Schema Quick View

### Main Tables

**users** - User accounts

```
id | username | email | password | role | credibility_score | created_at
```

**products** - Product listings

```
id | brand_id | category_id | name | slug | description | price | status
```

**reviews** - Product reviews

```
id | user_id | product_id | title | description | video | rating | helpful_votes
```

**brands** - Brand profiles

```
id | user_id | name | slug | description | logo | website
```

**categories** - Product categories

```
id | name | slug | icon
```

---

## 🐛 Troubleshooting

### Problem: "Database connection failed"

**Solution:**

1. Open XAMPP Control Panel
2. Ensure MySQL shows "Running" (green)
3. If not, click "Start"
4. Refresh browser

### Problem: "Database connection refused"

**Solution:**

1. Check MySQL is actually running
2. Check if `config/app.php` has correct settings:
   - `DB_HOST` should be `localhost`
   - `DB_USER` should be `root` (for XAMPP)
3. Try connecting via phpMyAdmin:
   - `http://localhost/phpmyadmin`
   - If this works, ReelProof will work

### Problem: "404 page not found"

**Solution:**

1. Verify file: `public/.htaccess` exists
2. Ensure Apache mod_rewrite is enabled
3. Check URL has `/public/` in path:
   - ✅ `http://localhost/reelproof/public/reviews`
   - ❌ `http://localhost/reelproof/reviews`

### Problem: "No tables found in database"

**Solution:**

1. Re-run migration from Step 2
2. In phpMyAdmin, select database → Import
3. Choose `database/migrations/001_create_tables.sql`
4. Click "Go"

### Problem: Blank page or 500 error

**Solution:**

1. Enable debug mode in `config/app.php`:
   ```php
   define('APP_DEBUG', true);
   ```
2. Refresh page to see error details
3. Check `xampp/php/logs/php_error_log`

---

## 🚀 Next Steps

1. **Register an account** - Test the auth system
2. **Explore the routes** - Check all URLs in `routes/web.php`
3. **Review the code** - Read through controllers and models
4. **Read documentation** - Check files in root:
   - `PROJECT_DOCUMENTATION.md` - Full overview
   - `DATABASE_CONFIG_GUIDE.md` - DB configuration
   - `ARCHITECTURE.md` - System architecture

---

## 📞 Quick Reference URLs

| URL                                          | Purpose         |
| -------------------------------------------- | --------------- |
| `http://localhost/reelproof/public`          | Homepage        |
| `http://localhost/reelproof/public/register` | Sign up         |
| `http://localhost/reelproof/public/login`    | Log in          |
| `http://localhost/phpmyadmin`                | Database admin  |
| `http://localhost/xampp/`                    | XAMPP dashboard |

---

## ✨ You're All Set!

Your ReelProof project is ready to run. Simply:

1. ✅ Start XAMPP (Apache + MySQL)
2. ✅ Create database using migration
3. ✅ Visit `http://localhost/reelproof/public`
4. ✅ Register and start exploring!

**Happy coding! 🎉**

---

**Version:** v0.0.0  
**Last Updated:** April 29, 2026
