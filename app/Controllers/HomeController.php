<?php

class HomeController extends Controller
{
    private ReviewModel   $reviews;
    private CategoryModel $categories;
    private BrandModel    $brands;

    public function __construct()
    {
        $this->reviews    = new ReviewModel();
        $this->categories = new CategoryModel();
        $this->brands     = new BrandModel();
    }

    public function index(Request $request): void
    {
        $sort        = $request->input('sort', 'latest');
        $page        = (int)$request->input('page', 1);
        $feed        = $this->reviews->getFeedPaginated($page, null, $sort);
        $categories  = $this->categories->getAllWithCount();
        $topBrands   = $this->brands->getWithStats(1);

        $this->view('home.index', [
            'title'      => 'ReelProof — Real Reviews, Real People',
            'feed'       => $feed,
            'categories' => $categories,
            'topBrands'  => array_slice($topBrands, 0, 6),
            'sort'       => $sort,
        ]);
    }

    public function explore(Request $request): void
    {
        $page       = (int)$request->input('page', 1);
        $sort       = $request->input('sort', 'trending');
        $categoryId = $request->input('category') ? (int)$request->input('category') : null;
        $feed       = $this->reviews->getFeedPaginated($page, $categoryId, $sort);
        $categories = $this->categories->getAllWithCount();

        $this->view('home.explore', [
            'title'      => 'Explore Reviews',
            'feed'       => $feed,
            'categories' => $categories,
            'sort'       => $sort,
            'category'   => $categoryId,
        ]);
    }

    public function search(Request $request): void
    {
        $query   = $request->input('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            $reviewModel  = new ReviewModel();
            $productModel = new ProductModel();
            $results = [
                'reviews'  => $reviewModel->search($query),
                'products' => $productModel->search($query),
            ];
        }

        $this->view('home.search', [
            'title'   => $query ? "Search: {$query}" : 'Search',
            'query'   => $query,
            'results' => $results,
        ]);
    }

    public function category(Request $request): void
    {
        $slug     = $request->param('slug');
        $catModel = new CategoryModel();
        $category = $catModel->findBySlug($slug);

        if (!$category) $this->abort(404);

        $page = (int)$request->input('page', 1);
        $feed = $this->reviews->getFeedPaginated($page, $category['id'], 'latest');

        $this->view('home.category', [
            'title'    => $category['name'] . ' Reviews',
            'category' => $category,
            'feed'     => $feed,
        ]);
    }
}
