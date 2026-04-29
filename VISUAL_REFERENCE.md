# 📖 ReelProof Visual Reference Guide

**Quick visual guides for the ReelProof project structure and workflow**

---

## 📁 Directory Tree with Descriptions

```
reelproof/                               ← Project root
│
├── 📄 README.md                         ← START HERE (documentation index)
├── 📄 QUICK_START.md                    ← 5-step setup guide
├── 📄 PROJECT_DOCUMENTATION.md          ← Complete project reference
├── 📄 ARCHITECTURE.md                   ← System design & flow
├── 📄 DATABASE_CONFIG_GUIDE.md          ← DB configuration
├── 📄 CONFIGURATION_STATUS.md           ← Setup verification
├── 📄 ANALYSIS_SUMMARY.md               ← Project analysis
│
├── 📁 app/                              ← Application code
│   ├── 📁 Controllers/                  ← Request handlers
│   │   ├── AuthController.php           (login/register)
│   │   ├── HomeController.php           (homepage/explore)
│   │   ├── ReviewController.php         (reviews CRUD)
│   │   └── OtherControllers.php         (other features)
│   │
│   ├── 📁 Core/                         ← Framework foundation
│   │   ├── Autoloader.php               (PSR-4 loading)
│   │   ├── Router.php                   (URL routing)
│   │   ├── Database.php                 (PDO singleton)
│   │   ├── Model.php                    (Base model class)
│   │   ├── Controller.php               (Base controller)
│   │   ├── Request.php                  (HTTP request)
│   │   ├── Response.php                 (HTTP response)
│   │   └── Session.php                  (Session manager)
│   │
│   ├── 📁 Models/                       ← Data access
│   │   ├── UserModel.php                (User queries)
│   │   ├── ReviewModel.php              (Review queries)
│   │   ├── ProductModel.php             (Product queries)
│   │   ├── BrandModel.php               (Brand queries)
│   │   └── CategoryModel.php            (Category queries)
│   │
│   ├── 📁 Middleware/                   ← Request filters
│   │   └── AuthMiddleware.php           (Auth check)
│   │
│   └── 📁 Helpers/                      ← Utilities
│       └── helpers.php                  (Helper functions)
│
├── 📁 config/                           ← Configuration
│   └── app.php                          ← ⭐ MAIN CONFIG (DB, URL, etc.)
│
├── 📁 database/                         ← Database schema
│   └── migrations/
│       └── 001_create_tables.sql        (Database structure)
│
├── 📁 public/                           ← Web root (HTTP accessible)
│   ├── index.php                        ← ⭐ ENTRY POINT
│   ├── .htaccess                        (Apache URL rewriting)
│   ├── 📁 css/
│   │   └── main.css                     (Styles)
│   └── 📁 js/
│       └── main.js                      (Scripts)
│
├── 📁 routes/                           ← URL routing
│   └── web.php                          (All routes defined)
│
└── 📁 views/                            ← HTML templates
    ├── 📁 auth/
    │   ├── login.php                    (Login form)
    │   └── register.php                 (Register form)
    │
    ├── 📁 home/
    │   ├── index.php                    (Homepage)
    │   ├── explore.php                  (Explore page)
    │   ├── category.php                 (Category view)
    │   └── search.php                   (Search results)
    │
    ├── 📁 reviews/
    │   ├── index.php                    (Reviews list)
    │   ├── show.php                     (Single review)
    │   ├── create.php                   (Create form)
    │   ├── edit.php                     (Edit form)
    │   └── _card.php                    (Review component)
    │
    ├── 📁 products/
    │   ├── index.php                    (Products list)
    │   └── show.php                     (Product detail)
    │
    ├── 📁 brands/
    │   ├── index.php                    (Brands list)
    │   ├── show.php                     (Brand detail)
    │   ├── dashboard.php                (Brand dashboard)
    │   └── create_product.php           (Create product)
    │
    ├── 📁 dashboard/
    │   ├── buyer.php                    (Buyer dashboard)
    │   └── reviewer.php                 (Reviewer dashboard)
    │
    ├── 📁 profile/
    │   ├── show.php                     (User profile)
    │   └── settings.php                 (Settings page)
    │
    ├── 📁 layouts/
    │   ├── header.php                   (Header layout)
    │   └── footer.php                   (Footer layout)
    │
    └── 📁 errors/
        └── 404.php                      (404 page)
```

---

## 🔄 Request Processing Flow (Visual)

```
┌─────────────────────────────────────────────────────────────┐
│                   BROWSER REQUEST                           │
│                  GET /products/mouse                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│          Apache Web Server (localhost)                      │
│  Receives request → Routes to public/index.php            │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│             public/index.php (Bootstrap)                   │
│  ├─ Define constants from config/app.php                  │
│  ├─ Register autoloader                                    │
│  ├─ Start session                                          │
│  ├─ Load all core classes                                 │
│  ├─ Load routes from routes/web.php                      │
│  └─ Create router instance                               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Router::dispatch(Request)                     │
│  ├─ Parse URI: /products/mouse                           │
│  ├─ Loop through all routes                              │
│  ├─ Match: GET /products/{slug}                          │
│  ├─ Extract params: slug = 'mouse'                       │
│  └─ Found! Proceed...                                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│            Run Middleware (if any)                         │
│  ├─ Check if route requires auth                         │
│  └─ AuthMiddleware: Validate session                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│            Resolve & Call Controller                       │
│  ├─ Handler: ProductController@show                      │
│  ├─ new ProductController()                              │
│  └─ $controller->show($request)                          │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│           ProductController::show()                        │
│  ├─ Get param: $slug = $request->param('slug')          │
│  ├─ Query: (new ProductModel())->findBy('slug', $slug)  │
│  ├─ Query: (new ReviewModel())->findAllBy(...)          │
│  ├─ Validate: if (!$product) abort(404)                 │
│  └─ Render: $this->view('products.show', [...])         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Database Queries (PDO)                        │
│  ├─ Database::getInstance() - Get singleton connection   │
│  ├─ $stmt = $pdo->prepare(sql)                          │
│  ├─ $stmt->execute(params)                              │
│  └─ $results = $stmt->fetchAll()                        │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              MySQL Database Execution                      │
│  ├─ SELECT * FROM products WHERE slug = ?               │
│  ├─ SELECT * FROM reviews WHERE product_id = ?          │
│  └─ Return rows to PDO                                   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Return to Controller                          │
│  ├─ $product = array(...)                                │
│  ├─ $reviews = array(array(...), array(...), ...)        │
│  └─ Call view rendering                                 │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│            View Rendering (Template Engine)               │
│  ├─ Extract data variables:                             │
│  │   $product, $reviews, $user, etc.                    │
│  ├─ Require views/products/show.php                    │
│  ├─ Output HTML (with variable substitution)            │
│  └─ Return HTML string                                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│               HTTP Response (Headers)                      │
│  ├─ HTTP/1.1 200 OK                                      │
│  ├─ Content-Type: text/html; charset=utf-8              │
│  ├─ Set-Cookie: reelproof_session=abc123...             │
│  └─ Content-Length: 15234                               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              HTTP Response (Body)                          │
│  ├─ <!DOCTYPE html>                                       │
│  ├─ <html>                                                │
│  ├─ <head>...</head>                                      │
│  ├─ <body>                                                │
│  │   ... Product details and reviews ...                 │
│  ├─ </body>                                               │
│  └─ </html>                                               │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              BROWSER RENDERS PAGE                          │
│  └─ User sees product detail with reviews               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema Visual

```
┌──────────────────────────────────────────────────────────────────┐
│                    DATABASE: reelproof                           │
└──────────────────────────────────────────────────────────────────┘

┌─────────────────────┐
│      USERS          │
├─────────────────────┤
│ id (PK)             │
│ username (UNIQUE)   │
│ email (UNIQUE)      │
│ password (hashed)   │◄──┐
│ role (enum)         │   │
│ credibility_score   │   │
│ created_at          │   │
│ updated_at          │   │
└─────────────────────┘   │
         │                │
         │        ┌───────┴────────────┐
         │        │                    │
         ▼        ▼                    │
┌─────────────────────┐    ┌──────────────────────┐
│    REVIEWS          │    │      BRANDS          │
├─────────────────────┤    ├──────────────────────┤
│ id (PK)             │    │ id (PK)              │
│ user_id (FK)◄───┐   │    │ user_id (FK)◄─────┐ │
│ product_id (FK)─┼─┐ │    │ name                 │ │
│ title           │ │ │    │ slug                 │ │
│ description     │ │ │    │ logo                 │ │
│ rating          │ │ │    │ created_at           │ │
│ video           │ │ │    │ updated_at           │ │
│ helpful_votes   │ │ │    └──────────────────────┘ │
│ created_at      │ │ │         │                   │
│ updated_at      │ │ │         ▼                   │
└─────────────────┘ │ │  ┌──────────────────────┐   │
         │          │ │  │     PRODUCTS         │   │
         │          │ │  ├──────────────────────┤   │
         │          │ │  │ id (PK)              │   │
         ▼          │ │  │ brand_id (FK)◄──────┤   │
┌─────────────────────┐  │ category_id (FK) ────┼─┐ │
│   REVIEW_VOTES      │  │ name                 │ │ │
├─────────────────────┤  │ slug                 │ │ │
│ id (PK)             │  │ description          │ │ │
│ review_id (FK)◄─────┘  │ price                │ │ │
│ user_id (FK)────────┐  │ status               │ │ │
│ vote_type           │  │ created_at           │ │ │
│ created_at          │  │ updated_at           │ │ │
└─────────────────────┘  └──────────────────────┘ │ │
                                   │              │ │
                                   ▼              │ │
                        ┌──────────────────────┐  │ │
                        │    CATEGORIES        │  │ │
                        ├──────────────────────┤  │ │
                        │ id (PK)              │◄─┘ │
                        │ name                 │    │
                        │ slug                 │    │
                        │ icon                 │    │
                        │ created_at           │    │
                        │ updated_at           │    │
                        └──────────────────────┘    │
                                                   │
           ┌───────────────────────────────────────┘
           │
           └─ All tables use utf8mb4
              All tables use InnoDB
              All tables have indexes
              Foreign key constraints enabled
```

---

## 🔐 Authentication State Diagram

```
┌──────────────┐
│  Not Logged  │
│     In       │
└──────┬───────┘
       │
       ├─ GET /login           → Show login form
       │
       ├─ POST /login (valid)  ──┐
       │                         │
       │                   ┌─────▼────────┐
       │                   │  Processing  │
       │                   ├──────────────┤
       │                   │ Verify email │
       │                   │ Verify pwd   │
       │                   │ Create sess  │
       │                   └─────┬────────┘
       │                         │
       │                    ✓ Success
       │                         │
       │                         ▼
       │                   ┌──────────────────┐
       │                   │   LOGGED IN      │
       │                   │  Session exists  │
       │                   │  user_id set     │
       │                   │  user_role set   │
       │                   └────────┬─────────┘
       │                            │
       │           ┌────────────────┼────────────────┐
       │           │                │                │
       │        [24 hours]        Logout            Access
       │           │                │            Protected Page
       │           │                │                │
       │           ▼                ▼                ▼
       │        Session          Session         Check Auth
       │        Expires          Destroyed       Middleware
       │           │                │                │
       │           │                │            ✓ Session
       │           │                │             exists
       │           │                │                │
       └───────────┴────────────────┴────────────────┴────────→ Allow Access
                                                           ✓ Proceed to controller
```

---

## 📊 Model Query Builder Pattern

```
┌─────────────────────────────────────────────┐
│        Model Class (extends Model)          │
├─────────────────────────────────────────────┤
│ protected string $table = 'products';       │
└────────────┬────────────────────────────────┘
             │
             ├─ find(int $id)
             │  └─ SELECT * FROM products WHERE id = ?
             │
             ├─ findBy(string $column, mixed $value)
             │  └─ SELECT * FROM products WHERE column = ?
             │
             ├─ findAllBy(string $column, mixed $value)
             │  └─ SELECT * FROM products WHERE column = ? (multiple)
             │
             ├─ all(string $orderBy)
             │  └─ SELECT * FROM products ORDER BY column
             │
             ├─ paginate(int $page, int $perPage)
             │  ├─ COUNT(*) for total
             │  └─ LIMIT & OFFSET for results
             │
             ├─ create(array $data)
             │  └─ INSERT INTO products (...) VALUES (...)
             │
             ├─ update(int $id, array $data)
             │  └─ UPDATE products SET ... WHERE id = ?
             │
             ├─ delete(int $id)
             │  └─ DELETE FROM products WHERE id = ?
             │
             └─ Custom methods
                └─ Add your own queries
```

---

## 🔗 Middleware Execution Order

```
Request
   │
   ▼
Route Matching ──────────────► Route not found? → 404 abort
   │
   ▼
Middleware Stack
   │
   ├─ AuthMiddleware (if required by route)
   │  │
   │  ├─ Session::has('user_id')?
   │  │  │
   │  │  ├─ NO  → Redirect to /login
   │  │  │
   │  │  └─ YES → Continue to next middleware
   │  │
   │  ▼
   │
   ├─ [Other middleware here]
   │
   ▼
Controller Method
   │
   ├─ Validate request
   ├─ Query database
   ├─ Process data
   ├─ Render view OR return JSON
   │
   ▼
Response
```

---

## 📝 View Rendering Process

```
Controller::view('products.show', $data)
         │
         ├─ Extract data array keys as variables:
         │  ├─ $data['product'] → $product
         │  ├─ $data['reviews'] → $reviews
         │  └─ $data['user'] → $user
         │
         ├─ Convert dot notation to path:
         │  products.show → products/show.php
         │
         ├─ Build full path:
         │  /var/www/html/reelproof/views/products/show.php
         │
         ├─ Check file exists:
         │  ├─ NO  → throw RuntimeException
         │  └─ YES → Continue
         │
         ├─ Extract variables into scope
         │
         ├─ require 'views/products/show.php'
         │
         ├─ PHP code in view executes:
         │  ├─ Use $product, $reviews, $user
         │  ├─ Output HTML
         │  └─ Include layouts/header.php
         │
         └─ Return HTML to browser
```

---

## 🎯 Role-Based Access Control

```
protected $role = 'buyer' (from session)

                    ┌────────────┐
                    │   ROUTE    │
                    └─────┬──────┘
                          │
                   ┌──────▼───────┐
                   │ No middleware?│
                   └──────┬───────┘
                          │
                   YES────┴────NO
                   │           │
              Allow ◄──────┐   │
                          │   ▼
                    ┌──────────────────────┐
                    │ Has AuthMiddleware?  │
                    └──────────┬───────────┘
                               │
                        Check::has('user_id')
                               │
                        ┌──────┴──────┐
                        │             │
                       NO            YES
                        │             │
                    Redirect      Continue
                    to /login         │
                                      ▼
                            ┌──────────────────────┐
                            │requireRole('brand')?│
                            └──────────┬───────────┘
                                       │
                               ┌───────┴────────┐
                               │                │
                         $role = 'brand'    $role != 'brand'
                               │                │
                            Allow         abort(403)
                                          Forbidden
```

---

## ⏱️ Request Timeline

```
0ms   ┌─ Browser sends request
      │
1ms   ├─ Apache receives
      │
2ms   ├─ Load config/app.php
      │
3ms   ├─ Boot framework (Router, Database, etc.)
      │
4ms   ├─ Parse routes/web.php
      │
5ms   ├─ Route matching
      │
6ms   ├─ Execute middleware
      │
7ms   ├─ Instantiate controller
      │
8-40ms├─ Database queries
      │  ├─ SELECT from products
      │  ├─ SELECT from reviews
      │  └─ Wait for results
      │
41ms  ├─ Process data in controller
      │
42ms  ├─ Render view
      │  ├─ Load template file
      │  ├─ Parse PHP code
      │  └─ Generate HTML
      │
43ms  ├─ Send response headers
      │
44ms  ├─ Send HTML body
      │
45ms  └─ Browser receives & renders page
```

---

## 🛡️ Security Flow

```
User Input
    │
    ▼
┌─────────────────────┐
│ Request::input()    │  ← Sanitizes whitespace
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│ Validation          │  ← Check format, length, type
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│ PDO Prepared        │  ← Bind parameters
│ Statements          │     Prevents SQL injection
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│ Password Hashing    │  ← bcrypt (if auth)
│ (bcrypt)            │
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│ Database Stored     │  ← Safely persist
└─────────────────────┘
```

---

## 🚀 Deployment Checklist

```
Development (Current)
├─ ✅ APP_DEBUG = true
├─ ✅ Error display ON
├─ ✅ DB user = root
├─ ✅ DB password = (empty)
└─ ✅ HTTP localhost

                    ▼

Production (Before deploy)
├─ ⚠️  Change APP_DEBUG to false
├─ ⚠️  Disable error display
├─ ⚠️  Change DB user to prod_user
├─ ⚠️  Set strong DB password
├─ ⚠️  Use HTTPS (not HTTP)
├─ ⚠️  Set APP_URL to production domain
├─ ⚠️  Configure error logging (file, not display)
├─ ⚠️  Set proper permissions
└─ ⚠️  Backup database before deploy
```

---

## 📚 Quick Lookup Table

| Need               | Location    | File                                        |
| ------------------ | ----------- | ------------------------------------------- |
| Configure Database | Top of file | `config/app.php`                            |
| Define Routes      | All routes  | `routes/web.php`                            |
| Add Controller     | New class   | `app/Controllers/MyController.php`          |
| Add Model          | New class   | `app/Models/MyModel.php`                    |
| Add View           | New file    | `views/my/file.php`                         |
| Database Schema    | SQL         | `database/migrations/001_create_tables.sql` |
| App Settings       | Constants   | `config/app.php`                            |
| CSS Styles         | Stylesheet  | `public/css/main.css`                       |
| JavaScript         | Script      | `public/js/main.js`                         |
| Error Handling     | Bootstrap   | `public/index.php`                          |
| Auth Check         | Middleware  | `app/Middleware/AuthMiddleware.php`         |

---

## ✨ This Visual Guide Covers

- ✅ Complete directory structure
- ✅ Request processing flow
- ✅ Database schema relationships
- ✅ Authentication state diagram
- ✅ Model query patterns
- ✅ Middleware execution
- ✅ View rendering process
- ✅ RBAC flow
- ✅ Request timeline
- ✅ Security mechanisms
- ✅ Deployment checklist
- ✅ Quick lookup table

---

**Created:** April 29, 2026  
**For:** ReelProof v0.0.0  
**Status:** Ready for Development 🟢
