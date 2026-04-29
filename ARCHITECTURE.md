# ReelProof Architecture Overview

## 🎯 Executive Summary

**ReelProof** is a **custom MVC framework** (not using Laravel/Symfony) built with vanilla PHP 8.1+. It's designed as a lightweight, production-ready foundation for a video-based product review platform.

---

## 🏗️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENT BROWSER                           │
│                   (HTML/CSS/JavaScript)                      │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP Request
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              public/index.php (Entry Point)                  │
│  - Initializes constants                                     │
│  - Loads core classes                                        │
│  - Starts session                                            │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│               Router (Route Matching)                        │
│  - Parses incoming URL                                       │
│  - Matches against registered routes                        │
│  - Extracts URL parameters {id}, {slug}, etc.              │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│            Middleware Stack (if defined)                     │
│  - AuthMiddleware: Check user login status                  │
│  - Custom middleware: Validate permissions                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Controller (Business Logic)                     │
│  - Process request data                                      │
│  - Interact with models                                      │
│  - Prepare response data                                     │
└────────────────────────┬────────────────────────────────────┘
                         │
          ┌──────────────┼──────────────┐
          ▼              ▼              ▼
    ┌──────────┐  ┌──────────┐  ┌──────────┐
    │  Model   │  │  Model   │  │  Model   │
    │(Query)   │  │ (Create) │  │(Update)  │
    └────┬─────┘  └────┬─────┘  └────┬─────┘
         │             │             │
         └─────────────┼─────────────┘
                       │
                       ▼
         ┌──────────────────────────────┐
         │   Database Layer (PDO)        │
         │  - Prepared statements        │
         │  - SQL execution              │
         │  - Connection pooling         │
         └──────────────┬────────────────┘
                        │
                        ▼
         ┌──────────────────────────────┐
         │   MySQL Database              │
         │  - Users                      │
         │  - Reviews                    │
         │  - Products                   │
         │  - Brands                     │
         │  - Categories                 │
         └──────────────────────────────┘
```

---

## 📊 Request Lifecycle (Detailed Flow)

```
1. Browser makes HTTP request
   GET /products/awesome-laptop

2. Apache routes to public/index.php

3. index.php includes:
   ├─ config/app.php (constants)
   ├─ app/Core/Autoloader.php (PSR-4 loading)
   ├─ app/Core/Database.php (DB connection)
   ├─ app/Core/Router.php (URL routing)
   ├─ app/Core/Request.php (HTTP wrapper)
   ├─ app/Core/Response.php (Response handler)
   └─ app/Core/Session.php (User sessions)

4. Load routes from routes/web.php
   $router->get('/products/{slug}', 'ProductController@show');

5. Router dispatcher runs
   - Parse URI: /products/awesome-laptop
   - Match route pattern: /products/{slug}
   - Extract param: slug = 'awesome-laptop'

6. Run middleware (if any)
   - AuthMiddleware checks Session::has('user_id')

7. Resolve handler: ProductController@show
   - Instantiate: $controller = new ProductController()
   - Call method: $controller->show($request)

8. Controller logic executes
   - $product = (new ProductModel())->findBy('slug', 'awesome-laptop')
   - $reviews = (new ReviewModel())->findAllBy('product_id', $product['id'])
   - $this->view('products.show', ['product' => $product, ...])

9. View rendered
   - Extract data variables
   - Render: views/products/show.php
   - Output HTML to browser

10. Response sent
    HTTP/1.1 200 OK
    Content-Type: text/html
    [HTML Content]
```

---

## 🗂️ Directory Responsibilities

### `/app` - Application Logic

```
app/
├── Controllers/          # Handle HTTP requests
│   ├── AuthController    # Login/Register logic
│   ├── HomeController    # Homepage, explore
│   ├── ReviewController  # Review CRUD
│   └── *Controller       # Other features
│
├── Core/                 # Framework foundation
│   ├── Router.php        # Route registration & dispatch
│   ├── Controller.php    # Base controller methods
│   ├── Model.php         # Base model with query builder
│   ├── Database.php      # PDO singleton
│   ├── Request.php       # HTTP request wrapper
│   ├── Response.php      # HTTP response utilities
│   ├── Session.php       # Session management
│   └── Autoloader.php    # PSR-4 auto-loading
│
├── Models/               # Data access objects
│   ├── UserModel         # User queries
│   ├── ReviewModel       # Review queries
│   ├── ProductModel      # Product queries
│   ├── BrandModel        # Brand queries
│   └── CategoryModel     # Category queries
│
├── Middleware/           # Request filters
│   └── AuthMiddleware    # Authentication check
│
└── Helpers/              # Utility functions
    └── helpers.php       # Global functions (slugify, etc.)
```

### `/config` - Configuration

```
config/
└── app.php              # All environment constants
```

### `/database` - Database Schema

```
database/
└── migrations/
    └── 001_create_tables.sql  # Initial schema
```

### `/public` - Web Root (Accessible via HTTP)

```
public/
├── index.php            # Entry point
├── .htaccess            # Apache URL rewriting
├── css/
│   └── main.css         # Global styles
└── js/
    └── main.js          # Global scripts
```

### `/routes` - Route Definitions

```
routes/
└── web.php              # All route definitions
```

### `/views` - HTML Templates

```
views/
├── auth/                # Login/Register forms
├── brands/              # Brand pages
├── dashboard/           # Dashboard pages
├── home/                # Homepage, explore
├── products/            # Product pages
├── profile/             # User profile
├── reviews/             # Review pages
├── errors/              # Error pages (404, etc.)
└── layouts/             # Header/footer
```

---

## 🔄 Data Flow Examples

### Example 1: User Registration Flow

```
1. Browser: GET /register
   └─> Show registration form (views/auth/register.php)

2. User fills form and submits
   POST /register with form data

3. Router matches POST /register
   └─> AuthController@register

4. AuthController::register() validates input
   - Check email not in use
   - Hash password with bcrypt
   - Prepare user data

5. Create new user via model
   (new UserModel())->create([
       'username' => 'john_doe',
       'email' => 'john@example.com',
       'password' => '$2y$10$...',  // hashed
       'role' => 'buyer'
   ])

6. Model executes INSERT query
   INSERT INTO users (username, email, password, role, created_at, updated_at)
   VALUES ('john_doe', 'john@example.com', ..., 'buyer', NOW(), NOW())

7. Database inserts row and returns new ID

8. Controller redirects to login
   $this->redirect('/login')

9. Browser receives redirect response
   HTTP 302 Location: /login
```

### Example 2: Viewing Product Reviews

```
1. Browser: GET /products/gaming-mouse

2. Router matches route
   └─> ProductController@show

3. ProductController extracts slug from URL
   $slug = $request->param('slug')  // 'gaming-mouse'

4. Fetch product from DB
   $product = (new ProductModel())->findBy('slug', 'gaming-mouse')

5. If product not found
   └─> $this->abort(404)  // Show 404 page

6. Fetch reviews for this product
   $reviews = (new ReviewModel())->findAllBy(
       'product_id',
       $product['id'],
       'created_at DESC'
   )

7. Render view with data
   $this->view('products.show', [
       'product' => $product,
       'reviews' => $reviews,
       'user'    => $this->currentUser()
   ])

8. View renders: views/products/show.php
   - Displays product details
   - Lists reviews
   - Shows review form if user authenticated

9. HTML returned to browser
```

### Example 3: Voting on a Review (with Authentication)

```
1. Browser: POST /reviews/42/vote
   with: { vote_type: 'helpful' }

2. Router matches, runs AuthMiddleware
   └─> Checks Session::has('user_id')
   └─> If not authenticated: redirect to login

3. Route resolved: ReviewController@vote

4. ReviewController::vote() executes
   $userId = Session::get('user_id')
   $reviewId = $request->param('id')  // 42
   $voteType = $request->input('vote_type')

5. Query if user already voted
   $existingVote = $this->db->prepare(
       "SELECT * FROM review_votes
        WHERE review_id = ? AND user_id = ?"
   )

6. If vote exists: update or delete
   If vote doesn't exist: insert new vote

7. Recalculate helpful votes count
   UPDATE reviews SET helpful_votes = helpful_votes + 1
   WHERE id = 42

8. Return JSON response
   $this->json([
       'success' => true,
       'helpful_votes' => $newCount
   ])

9. Browser receives JSON and updates UI
   JavaScript updates vote count display
```

---

## 🔐 Authentication & Authorization Architecture

### Session-Based Authentication

```
┌──────────────────────────────────────┐
│   User Login Page (views/auth/login) │
└────────────┬───────────────────────┬─┘
             │                       │
        SUBMIT FORM           Invalid Credentials
             │                       │
             ▼                       ▼
    ┌─────────────────┐    Redirect to /login
    │  AuthController │    with error message
    │   @login()      │
    └────────┬────────┘
             │
    ┌─────────────────────────────┐
    │ 1. Validate email exists    │
    │ 2. Fetch user from DB       │
    │ 3. Verify password (bcrypt) │
    └────────┬────────────────────┘
             │
        ✓ Password matches
             │
    ┌─────────────────────────────┐
    │   Create session:            │
    │  Session::set('user_id',    │
    │    $user['id'])             │
    │  Session::set('user_role',  │
    │    $user['role'])           │
    └────────┬────────────────────┘
             │
    ┌─────────────────────────────┐
    │  Redirect to /dashboard     │
    │  with success message       │
    └─────────────────────────────┘
             │
    ┌─────────────────────────────┐
    │  On subsequent requests:    │
    │  $_SESSION['user_id'] is    │
    │  available for 24 hours     │
    └─────────────────────────────┘
```

### Middleware-Based Authorization

```
Route Definition:
$router->post('/reviews', 'ReviewController@store', ['AuthMiddleware']);

When request comes in:
  │
  ├─> Instantiate AuthMiddleware
  ├─> Call handle($request)
  │
  ├─> Is user logged in?
  │
  ├─ YES → Continue to controller
  │
  └─ NO → Redirect to /login with error
```

### Role-Based Access Control

```
In Controller:
$this->requireRole('brand');  // Only 'brand' role can proceed

Check Flow:
  1. Is authenticated? → If no, redirect to login
  2. Is correct role? → If no, abort(403) Forbidden

Database stores roles as ENUM:
  - 'buyer' (default) → Can view reviews, vote
  - 'reviewer' → Can create/edit reviews
  - 'brand' → Can manage products
  - 'admin' → (future) Full access
```

---

## 💾 Database Access Pattern

### Generic Model Methods (Available to All Models)

```php
// Base Model class defines:
$model->find($id)                          // SELECT * WHERE id = ?
$model->findBy($column, $value)           // SELECT * WHERE column = ?
$model->findAllBy($column, $value)        // SELECT * WHERE column = ? (multiple)
$model->all($orderBy)                     // SELECT * ORDER BY
$model->paginate($page, $perPage)         // SELECT * LIMIT & OFFSET
$model->create($data)                     // INSERT
$model->update($id, $data)                // UPDATE
$model->delete($id)                       // DELETE
```

### Custom Model Methods

Each model can add custom queries:

```php
class ReviewModel extends Model {
    protected string $table = 'reviews';

    // Custom query for trending reviews
    public function getTrending($limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.username, p.name
             FROM {$this->table} r
             JOIN users u ON r.user_id = u.id
             JOIN products p ON r.product_id = p.id
             WHERE r.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
             ORDER BY r.helpful_votes DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
```

### Query Execution Safety

All queries use **prepared statements** (parameterized):

```php
// ✅ SAFE - Prevents SQL injection
$stmt = $this->db->prepare(
    "SELECT * FROM users WHERE email = ?"
);
$stmt->execute([$userEmail]);

// ❌ DANGEROUS - String interpolation
$result = $this->db->query(
    "SELECT * FROM users WHERE email = '$userEmail'"
);
```

---

## 🎨 View Rendering System

### View Method

```php
// In Controller:
$this->view('products.show', [
    'product' => $productData,
    'reviews' => $reviewsArray,
    'user' => $currentUser
]);
```

### View Resolution

```
View Path: 'products.show'
      ↓
Replace dots with slashes: 'products/show'
      ↓
Full path: /views/products/show.php
      ↓
Check file exists:
  - If exists: Require and render
  - If not: Throw RuntimeException

Data extraction:
  - All array keys become variables
  - $product = $productData
  - $reviews = $reviewsArray
  - $user = $currentUser
```

### View Nesting

```
views/layouts/header.php
  ↑
  │ included via:
  │ require 'views/layouts/header.php'
  │
views/products/show.php (main view)
  │
  ├─ includes: layouts/header.php
  ├─ loops: reviews
  │   └─ includes: reviews/_card.php (component)
  └─ includes: layouts/footer.php
```

---

## 🚀 Request Handling Pipeline

### Full Request Lifecycle with Timeline

```
T+0ms    Browser sends HTTP request
         └─> GET /products/awesome-laptop

T+1ms    Web server (Apache) receives
         └─> Routes to public/index.php

T+2ms    index.php executes:
         ├─ Define constants (config/app.php)
         ├─ Register autoloader
         ├─ Create DB connection (first call only)
         ├─ Start session
         └─ Load routes

T+3ms    Create Router instance
         └─> Parse routes/web.php

T+4ms    Call $router->dispatch($request)
         ├─ Get request URI
         ├─ Match against routes
         ├─ Extract parameters
         └─ Find matching route

T+5ms    Execute middleware (if any)
         └─> Check Session::has('user_id')

T+6ms    Resolve handler
         └─> Instantiate ProductController
         └─> Call show() method

T+7-50ms Controller logic runs
         ├─ Query database
         │  └─> SELECT * FROM products WHERE slug = ?
         │  └─> SELECT * FROM reviews WHERE product_id = ?
         ├─ Process data
         ├─ Call view rendering
         └─ Extract view variables

T+51ms   View file executed
         ├─ Read views/products/show.php
         ├─ Output HTML markup
         └─ Return to controller

T+52ms   Response headers set
         ├─ HTTP/1.1 200 OK
         ├─ Content-Type: text/html; charset=utf-8
         └─ Set-Cookie: reelproof_session=...

T+53ms   Send response body to browser

T+54ms   Browser receives HTML
         └─> Render page to user
```

---

## 🔍 Error Handling

### Development Mode (APP_DEBUG = true)

```php
// config/app.php
define('APP_DEBUG', true);

Exception occurs:
  ├─ ini_set('display_errors', 1)
  ├─ error_reporting(E_ALL)
  └─ Full error trace displayed to screen
     └─> File, line number, stack trace
     └─> Shows sensitive info (paths, configs)
```

### Production Mode (APP_DEBUG = false)

```php
// config/app.php
define('APP_DEBUG', false);

Exception occurs:
  ├─ ini_set('display_errors', 0)
  ├─ error_reporting(0)
  └─ Generic error message to user
     └─> Logs details to file (not visible)
```

### Exception Handling

```php
try {
    $db = Database::getInstance();
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die('Database connection failed: ' . $e->getMessage());
    }
    die('Service temporarily unavailable.');
}
```

---

## 🛠️ Framework vs Standalone Components

### What the Framework Provides

| Component  | Purpose                | Customizable              |
| ---------- | ---------------------- | ------------------------- |
| Router     | URL routing & dispatch | ✅ Add new methods        |
| Model      | Query builder & ORM    | ✅ Override in subclasses |
| Controller | Request handling       | ✅ Extend base            |
| Database   | PDO connection         | ⚠️ Singleton pattern      |
| Session    | User sessions          | ✅ Add methods            |
| View       | Template rendering     | ✅ Add view paths         |

### What You Implement

- Controllers (business logic)
- Models (data access customization)
- Views (HTML templates)
- Routes (URL patterns)
- Middleware (custom filters)
- Helpers (utility functions)

---

## 📈 Scalability Considerations

### Current Limitations

- Single PDO connection (OK for small apps)
- No query caching layer
- All models load entire rows
- No API versioning system

### Future Enhancements

- Connection pooling
- Redis caching layer
- Query optimization (select specific columns)
- API versioning (v1/, v2/)
- Queue system for video processing

---

## 🎯 Summary

**ReelProof Framework is:**

✅ **Lightweight** - No heavy dependencies  
✅ **Understandable** - All code is readable, not black-box  
✅ **Extensible** - Easy to add features  
✅ **Production-ready** - Prepared statements, error handling  
✅ **Educational** - Great for learning MVC patterns

**Not suitable for:**

❌ Real-time applications (no WebSockets)  
❌ High-frequency APIs (consider caching layer)  
❌ Enterprise systems (consider Laravel/Symfony)

---

**Document Version:** 1.0  
**Last Updated:** April 29, 2026
