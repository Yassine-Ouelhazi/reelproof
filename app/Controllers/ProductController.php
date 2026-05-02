<?php

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
