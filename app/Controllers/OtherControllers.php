<?php

// ─────────────────────────────────────────────────────────────────────────────
// ProductController
// ─────────────────────────────────────────────────────────────────────────────
class ProductController extends Controller
{
    private ProductModel $products;
    private ReviewModel  $reviews;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->reviews  = new ReviewModel();
    }

    public function index(Request $request): void
    {
        $page       = (int)$request->input('page', 1);
        $categoryId = $request->input('category') ? (int)$request->input('category') : null;
        $products   = $this->products->getWithStats($page, $categoryId);
        $categories = (new CategoryModel())->getAllWithCount();

        $this->view('products.index', [
            'title'      => 'Products',
            'products'   => $products,
            'categories' => $categories,
            'category'   => $categoryId,
        ]);
    }

    public function show(Request $request): void
    {
        $slug    = $request->param('slug');
        $product = $this->products->findBySlug($slug);

        if (!$product) $this->abort(404);

        $page    = (int)$request->input('page', 1);
        $reviews = $this->reviews->getByProduct($product['id'], $page);

        $this->view('products.show', [
            'title'   => $product['name'] . ' Reviews',
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
}


// ─────────────────────────────────────────────────────────────────────────────
// BrandController
// ─────────────────────────────────────────────────────────────────────────────
class BrandController extends Controller
{
    private BrandModel   $brands;
    private ProductModel $products;

    public function __construct()
    {
        $this->brands   = new BrandModel();
        $this->products = new ProductModel();
    }

    public function index(Request $request): void
    {
        $page   = (int)$request->input('page', 1);
        $brands = $this->brands->getWithStats($page);

        $this->view('brands.index', [
            'title'  => 'Brands',
            'brands' => $brands,
        ]);
    }

    public function show(Request $request): void
    {
        $slug  = $request->param('slug');
        $brand = $this->brands->findBySlug($slug);

        if (!$brand) $this->abort(404);

        $products = $this->products->getByBrand($brand['id']);
        $reviewModel = new ReviewModel();

        $this->view('brands.show', [
            'title'    => $brand['name'],
            'brand'    => $brand,
            'products' => $products,
        ]);
    }

    public function dashboard(Request $request): void
    {
        $this->requireRole('brand');
        $brand = $this->brands->findByUserId(Session::get('user_id'));
        if (!$brand) $this->abort(404);

        $products    = $this->products->getByBrand($brand['id']);
        $reviewModel = new ReviewModel();
        $recentReviews = [];
        foreach ($products as $product) {
            $reviews = $reviewModel->getByProduct($product['id']);
            $recentReviews = array_merge($recentReviews, $reviews);
        }

        usort($recentReviews, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        $this->view('brands.dashboard', [
            'title'         => 'Brand Dashboard',
            'brand'         => $brand,
            'products'      => $products,
            'recentReviews' => array_slice($recentReviews, 0, 10),
        ]);
    }

    public function createProduct(Request $request): void
    {
        $this->requireRole('brand');
        $brand      = $this->brands->findByUserId(Session::get('user_id'));
        $categories = (new CategoryModel())->all();

        $this->view('brands.create_product', [
            'title'      => 'Add Product',
            'brand'      => $brand,
            'categories' => $categories,
        ]);
    }

    public function storeProduct(Request $request): void
    {
        $this->requireRole('brand');
        $brand = $this->brands->findByUserId(Session::get('user_id'));

        $errors = $request->validate([
            'name'        => 'required|min:2|max:120',
            'description' => 'required|min:10',
            'category_id' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->redirectBack();
        }

        $imagePath = 'uploads/thumbnails/default-product.png';
        $imageFile = $request->file('image');
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($imageFile, 'uploads/thumbnails', ALLOWED_IMAGE_TYPES, MAX_THUMBNAIL_SIZE);
            if ($upload['success']) $imagePath = $upload['path'];
        }

        $this->products->createWithSlug([
            'brand_id'    => $brand['id'],
            'category_id' => (int)$request->input('category_id'),
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'image'       => $imagePath,
            'price'       => $request->input('price', 0),
            'status'      => 'active',
        ]);

        Session::flash('success', 'Product added successfully.');
        $this->redirect('/brand/dashboard');
    }
}


// ─────────────────────────────────────────────────────────────────────────────
// DashboardController
// ─────────────────────────────────────────────────────────────────────────────
class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $this->requireAuth();
        $role = Session::get('user_role');

        if ($role === 'brand') {
            $this->redirect('/brand/dashboard');
        }

        $userModel = new UserModel();
        $user      = $this->currentUser();

        if ($role === 'reviewer') {
            $reviewModel = new ReviewModel();
            $reviews     = $reviewModel->getByUser($user['id']);
            $this->view('dashboard.reviewer', [
                'title'   => 'My Dashboard',
                'user'    => $user,
                'reviews' => $reviews,
            ]);
        } else {
            $this->view('dashboard.buyer', [
                'title' => 'My Dashboard',
                'user'  => $user,
            ]);
        }
    }
}


// ─────────────────────────────────────────────────────────────────────────────
// ProfileController
// ─────────────────────────────────────────────────────────────────────────────
class ProfileController extends Controller
{
    public function show(Request $request): void
    {
        $username  = $request->param('username');
        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if (!$user) $this->abort(404);

        $reviews = [];
        if ($user['role'] === 'reviewer') {
            $reviewModel = new ReviewModel();
            $reviews     = $reviewModel->getByUser($user['id']);
        }

        $this->view('profile.show', [
            'title'   => '@' . $user['username'],
            'profile' => $user,
            'reviews' => $reviews,
        ]);
    }

    public function settings(Request $request): void
    {
        $user = $this->currentUser();
        $this->view('profile.settings', [
            'title' => 'Settings',
            'user'  => $user,
        ]);
    }

    public function update(Request $request): void
    {
        $this->requireAuth();
        $userId = Session::get('user_id');

        $errors = $request->validate([
            'full_name' => 'max:80',
            'bio'       => 'max:300',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->redirectBack();
        }

        $updateData = [
            'full_name' => $request->input('full_name', ''),
            'bio'       => $request->input('bio', ''),
        ];

        // Avatar upload
        $avatarFile = $request->file('avatar');
        if ($avatarFile && $avatarFile['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($avatarFile, 'uploads/avatars', ALLOWED_IMAGE_TYPES, MAX_AVATAR_SIZE);
            if ($upload['success']) {
                $updateData['avatar'] = $upload['path'];
            }
        }

        // Password change
        if ($request->input('new_password')) {
            if (strlen($request->input('new_password')) < 8) {
                Session::flash('error', 'New password must be at least 8 characters.');
                $this->redirectBack();
            }
            $updateData['password'] = password_hash($request->input('new_password'), PASSWORD_ARGON2ID);
        }

        $userModel = new UserModel();
        $userModel->update($userId, $updateData);

        Session::flash('success', 'Profile updated.');
        $this->redirect('/settings');
    }
}
