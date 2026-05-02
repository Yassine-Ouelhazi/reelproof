<?php

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
