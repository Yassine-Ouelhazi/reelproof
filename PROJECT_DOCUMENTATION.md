# Project Documentation — ReelProof (concise)

Overview:

- This repository contains a Django rewrite of the original ReelProof application. The codebase is organized into Django apps: `home`, `products`, `brands`, `reviews`, `accounts`.

Key locations:

- Django project: `reelproof_django/`
- Apps: `reelproof_django/home`, `reelproof_django/products`, `reelproof_django/brands`, `reelproof_django/reviews`, `reelproof_django/accounts`
- Templates: `reelproof_django/templates/`
- Static: `reelproof_django/static/`

Important routes (examples):

- `/` — home product feed
- `/explore/` — explore page
- `/search/` — search
- `/category/<slug>/` — category
- `/brands/` and `/brands/<slug>/` — brands
- `/products/` and `/products/<slug>/` — products
- `/reviews/` — reviews
- `/accounts/*` — auth/profile flows

Notes:

- Redundant and PHP-specific docs were removed from the root and consolidated here. The repository root no longer contains PHP entry points.
- For migrations, fixtures, and environment-specific configuration see `reelproof_django/README_DJANGO.md`.

# ReelProof - Complete Project Documentation

**Version:** v0.0.0 (Configuration Phase)  
**Created:** April 2026  
**Type:** PHP MVC Web Application  
**Purpose:** Real-time product review platform with video reviews and credibility scoring

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture & Design](#architecture--design)
3. [Directory Structure](#directory-structure)
4. [Core Components](#core-components)
5. [Database Schema](#database-schema)
6. [Feature Set](#feature-set)
7. [Routing System](#routing-system)
8. [Configuration](#configuration)
9. [Models & ORM](#models--orm)
10. [Authentication & Authorization](#authentication--authorization)
11. [API Response Format](#api-response-format)
12. [Setup Instructions](#setup-instructions)

---

## 🎯 Project Overview

**ReelProof** is a community-driven product review platform that emphasizes authentic, video-based reviews. The platform allows:

- **Reviewers**: Create detailed video reviews with credibility scoring
- **Buyers**: Explore products and watch real-world reviews before purchasing
- **Brands**: Manage products and monitor reviews in real-time
- **Community**: Vote on review helpfulness and build a credibility ecosystem

### Key Vision

- Reduce fake/biased reviews through credibility scoring
- Enable creators to monetize review creation
- Build trust through verified, real-person reviews
- Multi-role support (Reviewer, Buyer, Brand)

---

## 🏗️ Architecture & Design

### Design Pattern: **MVC (Model-View-Controller)**

```
HTTP Request
    ↓
Router (Route Matching)
    ↓
Middleware (Authentication/Authorization)
    ↓
Controller (Business Logic)
    ↓
Model (Database Operations)
    ↓
View (HTML/JSON Response)
    ↓
HTTP Response
```

### Technology Stack

| Layer              | Technology                       |
| ------------------ | -------------------------------- |
| **Backend**        | PHP 8.1+ (native, no framework)  |
| **Database**       | MySQL 8.0+                       |
| **Frontend**       | HTML5, CSS3, Vanilla JavaScript  |
| **ORM**            | Custom Query Builder (PDO-based) |
| **Session**        | Native PHP Sessions              |
| **Authentication** | Password hashing (bcrypt)        |

---

## 📁 Directory Structure

```
reelproof/
├── app/                           # Application core
│   ├── Controllers/               # HTTP request handlers
│   │   ├── AuthController.php     # Login/Register
│   │   ├── HomeController.php     # Homepage, explore, search
│   │   ├── ReviewController.php   # Review CRUD operations
│   │   └── OtherControllers.php   # Additional controllers
│   │
│   ├── Core/                      # Framework core
│   │   ├── Autoloader.php         # PSR-4 auto-loading
│   │   ├── Controller.php         # Base controller class
│   │   ├── Database.php           # PDO connection (Singleton)
│   │   ├── Model.php              # Base model with query builder
│   │   ├── Request.php            # HTTP request wrapper
│   │   ├── Response.php           # HTTP response utilities
│   │   ├── Router.php             # Route registration & dispatch
│   │   └── Session.php            # Session management
│   │
│   ├── Models/                    # Data models
│   │   ├── BrandModel.php         # Brand operations
│   │   ├── CategoryModel.php      # Category operations
│   │   ├── ProductModel.php       # Product operations
│   │   ├── ReviewModel.php        # Review operations
│   │   └── UserModel.php          # User operations
│   │
│   ├── Middleware/                # Request filters
│   │   └── AuthMiddleware.php     # Authentication check
│   │
│   └── Helpers/                   # Utility functions
│       └── helpers.php            # Global helper functions
│
├── config/
│   └── app.php                    # App configuration (DB, secrets, limits)
│
├── database/
│   └── migrations/
│       └── 001_create_tables.sql  # Initial schema
│
├── public/                        # Web-accessible files
│   ├── index.php                  # Application entry point
│   ├── .htaccess                  # Apache URL rewriting
│   ├── css/
│   │   └── main.css               # Global styles
│   └── js/
│       └── main.js                # Global JavaScript
│
├── routes/
│   └── web.php                    # Route definitions
│
└── views/                         # HTML templates
    ├── auth/
    │   ├── login.php              # Login form
    │   └── register.php           # Registration form
    │
    ├── brands/
    │   ├── index.php              # Brand directory
    │   ├── dashboard.php          # Brand dashboard
    │   ├── create_product.php     # Create product form
    │   └── show.php               # Brand detail page
    │
    ├── dashboard/
    │   ├── buyer.php              # Buyer dashboard
    │   └── reviewer.php           # Reviewer dashboard
    │
    ├── home/
    │   ├── index.php              # Homepage feed
    │   ├── explore.php            # Explore/discover page
    │   ├── category.php           # Category detail
    │   └── search.php             # Search results
    │
    ├── products/
    │   ├── index.php              # Products catalog
    │   └── show.php               # Product detail
    │
    ├── profile/
    │   ├── show.php               # User profile
    │   └── settings.php           # Profile settings
    │
    ├── reviews/
    │   ├── index.php              # Reviews listing
    │   ├── show.php               # Single review
    │   ├── create.php             # Create review form
    │   ├── edit.php               # Edit review form
    │   └── _card.php              # Review card component
    │
    ├── errors/
    │   └── 404.php                # 404 page
    │
    └── layouts/
        ├── header.php             # Header layout
        └── footer.php             # Footer layout
```

---

## ⚙️ Core Components

### 1. **Router** (`app/Core/Router.php`)

Handles URL routing with regex pattern matching and middleware support.

**Features:**

- HTTP method support: GET, POST, PUT, DELETE
- Named route parameters: `/reviews/{id}`
- Route grouping via middleware
- Automatic controller resolution

**Example Routes:**

```php
$router->get('/products/{slug}', 'ProductController@show');
$router->post('/reviews', 'ReviewController@store', ['AuthMiddleware']);
```

### 2. **Database** (`app/Core/Database.php`)

Singleton PDO connection manager with configuration from `config/app.php`.

**Features:**

- Single instance pattern (one connection per request)
- Automatic error handling
- UTF-8 charset enforcement
- PSR: Prepared statements for security

**Connection String:**

```
mysql:host=localhost;dbname=reelproof;charset=utf8mb4
```

### 3. **Model** (`app/Core/Model.php`)

Base class for all data models with query builder methods.

**Available Methods:**

| Method                                    | Purpose                         |
| ----------------------------------------- | ------------------------------- |
| `find(int $id)`                           | Get single record by ID         |
| `findBy(string $column, mixed $value)`    | Get single record by column     |
| `findAllBy(string $column, mixed $value)` | Get all records matching column |
| `all(string $orderBy)`                    | Get all records                 |
| `paginate(int $page, int $perPage)`       | Paginated results               |
| `create(array $data)`                     | Insert new record               |
| `update(int $id, array $data)`            | Update record                   |
| `delete(int $id)`                         | Delete record                   |

### 4. **Controller** (`app/Core/Controller.php`)

Base class for all controllers with view rendering and response utilities.

**Available Methods:**

| Method                            | Purpose                 |
| --------------------------------- | ----------------------- |
| `view(string $view, array $data)` | Render view template    |
| `redirect(string $url)`           | HTTP redirect           |
| `json(array $data, int $status)`  | JSON response           |
| `isAuthenticated()`               | Check if user logged in |
| `currentUser()`                   | Get logged-in user      |
| `requireAuth()`                   | Enforce login           |
| `requireRole(string $role)`       | Enforce role            |

### 5. **Request** (`app/Core/Request.php`)

Wrapper around HTTP request data ($\_GET, $\_POST, $\_FILES).

**Key Methods:**

- `method()` - HTTP method (GET, POST, etc.)
- `uri()` - Current URI path
- `input(string $key)` - Get input value
- `all()` - All input data
- `file(string $key)` - Get uploaded file

### 6. **Session** (`app/Core/Session.php`)

Session management wrapper.

**Key Methods:**

- `start()` - Initialize session
- `set(string $key, mixed $value)` - Store value
- `get(string $key)` - Retrieve value
- `has(string $key)` - Check existence
- `flash(string $key, mixed $value)` - One-time message

---

## 🗄️ Database Schema

### **Tables Overview**

#### **users**

Stores user accounts with roles and credibility tracking.

```sql
id (INT, PK)
username (VARCHAR 30, UNIQUE)
email (VARCHAR 180, UNIQUE)
password (VARCHAR 255, hashed)
full_name (VARCHAR 80)
bio (TEXT)
avatar (VARCHAR 255, path)
role (ENUM: 'reviewer', 'buyer', 'brand')
points (INT, for achievements)
credibility_score (TINYINT 0-100)
is_verified (BOOLEAN)
created_at, updated_at
```

**Indexes:** `role`, `credibility_score`

#### **categories**

Product categories with icons.

```sql
id, name (VARCHAR 80, UNIQUE slug)
icon (emoji/icon)
created_at, updated_at
```

#### **brands**

Brand profiles linked to users or standalone.

```sql
id, user_id (FK→users, nullable)
name (VARCHAR 120)
slug (VARCHAR 120, UNIQUE)
description (TEXT)
logo, website
created_at, updated_at
```

**Indexes:** `slug`

#### **products**

Product catalog with brand and category association.

```sql
id, brand_id (FK→brands), category_id (FK→categories)
name, slug (UNIQUE)
description (TEXT)
image, price (DECIMAL)
status (ENUM: 'active', 'inactive')
created_at, updated_at
```

**Indexes:** `slug`, `brand_id`, `category_id`

#### **reviews**

User reviews with video and metadata.

```sql
id, user_id (FK→users), product_id (FK→products)
title (VARCHAR 120)
description (TEXT)
video (VARCHAR 255, path)
thumbnail (VARCHAR 255, path)
rating (TINYINT 1-5)
helpful_votes, unhelpful_votes (INT)
created_at, updated_at
```

#### **review_votes**

Track individual votes on reviews.

```sql
id, review_id (FK→reviews), user_id (FK→users)
vote_type (ENUM: 'helpful', 'unhelpful')
created_at
```

**Unique Constraint:** `(review_id, user_id)` - User can only vote once per review

---

## 🎨 Feature Set

### **Phase 1 (Current - v0.0.0)**

- [x] User authentication (login/register)
- [x] Role-based access (Reviewer, Buyer, Brand)
- [x] Product catalog with categories
- [x] Brand management
- [x] Review creation interface
- [x] Review voting system
- [x] User profiles & settings
- [x] Dashboard (role-aware)

### **Phase 2 (Planned)**

- [ ] Video upload & processing (FFmpeg)
- [ ] Advanced search & filtering
- [ ] Review recommendations engine
- [ ] Credibility scoring algorithm
- [ ] Payment integration (Stripe)
- [ ] Email notifications
- [ ] Admin panel
- [ ] API endpoints (REST)

### **Phase 3 (Future)**

- [ ] Mobile app (React Native)
- [ ] AI review summarization
- [ ] Real-time collaboration
- [ ] Analytics dashboard
- [ ] Social sharing integration

---

## 🛣️ Routing System

### Route Definition Syntax

```php
$router->METHOD(path, handler, [middleware]);
```

- **METHOD**: `get`, `post`, `put`, `delete`
- **path**: URL path with optional `{param}` placeholders
- **handler**: `'ControllerName@methodName'`
- **middleware**: Array of middleware classes to run

### Complete Route Map

#### Authentication

```php
GET    /login                → Show login form
POST   /login                → Process login
GET    /register             → Show register form
POST   /register             → Process registration
POST   /logout               → Destroy session
```

#### Reviews

```php
GET    /reviews              → List all reviews
GET    /reviews/create       → Show create form (auth)
POST   /reviews              → Store review (auth)
GET    /reviews/{id}         → Show single review
GET    /reviews/{id}/edit    → Show edit form (auth)
POST   /reviews/{id}         → Update review (auth)
DELETE /reviews/{id}         → Delete review (auth)
POST   /reviews/{id}/vote    → Vote on review (auth)
```

#### Products & Brands

```php
GET    /products             → List products
GET    /products/{slug}      → Show product detail
GET    /brands               → List brands
GET    /brands/{slug}        → Show brand detail
```

#### Discovery

```php
GET    /                     → Homepage feed
GET    /explore              → Explore reviews
GET    /search               → Search results
GET    /category/{slug}      → Category reviews
```

#### Dashboards

```php
GET    /dashboard            → Role-aware dashboard (auth)
GET    /brand/dashboard      → Brand dashboard (auth)
GET    /brand/products/create → Create product (auth)
POST   /brand/products       → Store product (auth)
```

#### Profile

```php
GET    /profile/{username}   → User profile
GET    /settings             → Settings form (auth)
POST   /settings             → Update settings (auth)
```

---

## ⚙️ Configuration

Located in `config/app.php` - all app constants defined here.

### Database Configuration

```php
define('DB_HOST',    'localhost');    // MySQL host
define('DB_NAME',    'reelproof');    // Database name
define('DB_USER',    'root');         // MySQL user
define('DB_PASS',    '');             // MySQL password (empty for dev)
define('DB_CHARSET', 'utf8mb4');      // Character set
```

### Application Settings

```php
define('APP_ENV',      'development');  // Environment mode
define('APP_NAME',     'ReelProof');    // App name
define('APP_VERSION',  '1.0.0');        // Version string
define('APP_URL',      'http://localhost/reelproof/public');
define('APP_DEBUG',    true);           // Debug mode (show errors)
```

### Upload Limits

```php
define('MAX_VIDEO_SIZE',       524288000);  // 500MB
define('MAX_THUMBNAIL_SIZE',   5242880);    // 5MB
define('MAX_AVATAR_SIZE',      2097152);    // 2MB

define('ALLOWED_VIDEO_TYPES',  ['video/mp4', 'video/webm', 'video/quicktime']);
define('ALLOWED_IMAGE_TYPES',  ['image/jpeg', 'image/png', 'image/webp']);
```

### Session & Other

```php
define('SESSION_NAME',     'reelproof_session');
define('SESSION_LIFETIME', 86400);           // 24 hours
define('ITEMS_PER_PAGE',   12);              // Pagination
define('TIMEZONE',         'Africa/Tunis');
```

---

## 📊 Models & ORM

### Model Structure

All models extend `Model` base class and define `$table` property.

```php
class ReviewModel extends Model
{
    protected string $table = 'reviews';

    // Custom methods can be added
    public function getRecent(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
```

### Available Models

1. **UserModel** - User accounts, auth
2. **ReviewModel** - Reviews with custom queries
3. **ProductModel** - Products with brand/category relations
4. **BrandModel** - Brand management & stats
5. **CategoryModel** - Category browsing & counts

### Query Examples

```php
// Find
$user = (new UserModel())->find(1);
$user = (new UserModel())->findBy('email', 'user@example.com');

// Multiple records
$reviews = (new ReviewModel())->findAllBy('product_id', 5);
$all = (new BrandModel())->all('name ASC');

// Pagination
$paginated = (new ReviewModel())->paginate(
    page: 1,
    perPage: 12,
    where: 'status = ?',
    params: ['published']
);

// Create
$id = (new UserModel())->create([
    'username' => 'john_doe',
    'email'    => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_BCRYPT),
    'role'     => 'buyer',
]);

// Update
(new ReviewModel())->update(1, [
    'rating' => 5,
    'helpful_votes' => 42,
]);

// Delete
(new ReviewModel())->delete(1);
```

---

## 🔐 Authentication & Authorization

### Authentication Flow

1. User submits login form
2. `AuthController@login` validates credentials
3. Password verified with `password_verify()`
4. User session created via `Session::set()`
5. Redirect to dashboard

### Authorization Levels

| Role         | Permissions                                  |
| ------------ | -------------------------------------------- |
| **buyer**    | View reviews, create reviews on products     |
| **reviewer** | Full review management, credibility tracking |
| **brand**    | Manage products, view review metrics         |
| **admin**    | (Future) Moderation, user management         |

### Middleware Check

```php
// In routes
$router->post('/reviews', 'ReviewController@store', ['AuthMiddleware']);

// AuthMiddleware verifies session and halts if unauthorized
```

### Helper Methods in Controller

```php
$this->requireAuth();        // Halt if not logged in
$this->requireRole('brand'); // Halt if wrong role
$user = $this->currentUser(); // Get logged-in user
$this->isAuthenticated();    // Boolean check
```

---

## 📤 API Response Format

### HTML Views

Views use controller's `view()` method:

```php
$this->view('home.index', [
    'title' => 'Home',
    'reviews' => $reviews,
]);
// Renders: views/home/index.php with variables
```

### JSON Responses

```php
$this->json([
    'success' => true,
    'data' => $reviews,
], 200);
```

### Redirects

```php
$this->redirect('/dashboard');      // Redirect to URL
$this->redirectBack();              // Back to previous page
```

### Error Responses

```php
$this->abort(404);  // Show 404 page
```

---

## 🚀 Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache with mod_rewrite enabled
- Composer (optional, for dependencies)

### Step-by-Step Installation

#### 1. **Download/Clone Repository**

```bash
cd D:\xampp\htdocs
git clone https://github.com/yourrepo/reelproof.git
cd reelproof
```

#### 2. **Create Database**

Option A: Using phpMyAdmin

- Access `http://localhost/phpmyadmin`
- Create new database: `reelproof`
- Use charset: `utf8mb4`

Option B: Using MySQL CLI

```bash
mysql -u root -p < database/migrations/001_create_tables.sql
```

#### 3. **Configure Application**

Edit `config/app.php`:

```php
define('DB_HOST',   'localhost');
define('DB_NAME',   'reelproof');
define('DB_USER',   'root');
define('DB_PASS',   '');          // Empty for local dev
define('APP_URL',   'http://localhost/reelproof/public');
define('APP_DEBUG', true);        // Set to false in production
```

#### 4. **Set Directory Permissions**

```bash
# Make uploads directory writable
chmod -R 755 public/uploads/
chmod -R 755 storage/            # if using file storage
```

#### 5. **Start Apache & MySQL**

- Open XAMPP Control Panel
- Start Apache module
- Start MySQL module

#### 6. **Access Application**

Navigate to: `http://localhost/reelproof/public`

#### 7. **Create First User**

- Click "Register"
- Create account with role selection
- Log in and start exploring!

### Development Environment

**Local URL:** `http://localhost/reelproof/public`

**Database Connection Troubleshooting:**

If connection fails:

1. Verify MySQL is running in XAMPP
2. Check database exists: `SHOW DATABASES;`
3. Verify table exists: `USE reelproof; SHOW TABLES;`
4. Test connection in `config/app.php` constants
5. Check PHP error log: `xampp/php/logs/php_error_log`

### Running Migrations

To re-run database schema:

```sql
-- Option 1: Drop and recreate
DROP DATABASE reelproof;
CREATE DATABASE reelproof CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reelproof;
-- Run migration file
SOURCE /path/to/database/migrations/001_create_tables.sql;
```

---

## 📝 Development Notes

### Code Style

- PSR-4 Autoloading
- Type hints for parameters and returns
- Single Responsibility Principle (SRP)
- DRY (Don't Repeat Yourself)

### File Naming

- Controllers: `PascalCase` + `Controller.php`
- Models: `PascalCase` + `Model.php`
- Views: `snake_case.php`
- Database tables: `snake_case`, plural

### Testing Routes

Use browser or REST client:

```bash
# GET requests
curl http://localhost/reelproof/public/reviews

# POST requests
curl -X POST http://localhost/reelproof/public/login \
  -d "email=user@example.com&password=secret"
```

### Debugging

Enable in `config/app.php`:

```php
define('APP_DEBUG', true);
```

This shows detailed error messages during development.

---

## 📚 Additional Resources

- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [MySQL 8.0 Reference](https://dev.mysql.com/doc/)
- [HTTP Status Codes](https://httpwg.org/specs/rfc9110.html#status.codes)
- [MIME Types](https://www.iana.org/assignments/media-types/)

---

**Last Updated:** April 29, 2026  
**Maintained By:** ReelProof Development Team  
**Status:** Active Development (v0.0.0)
