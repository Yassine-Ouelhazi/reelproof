<?php

class ReviewController extends Controller
{
    private ReviewModel  $reviews;
    private ProductModel $products;

    public function __construct()
    {
        $this->reviews  = new ReviewModel();
        $this->products = new ProductModel();
    }

    public function index(Request $request): void
    {
        $page = (int)$request->input('page', 1);
        $sort = $request->input('sort', 'latest');
        $feed = $this->reviews->getFeedPaginated($page, null, $sort);

        $this->view('reviews.index', [
            'title' => 'All Reviews',
            'feed'  => $feed,
            'sort'  => $sort,
        ]);
    }

    public function show(Request $request): void
    {
        $id     = (int)$request->param('id');
        $review = $this->reviews->getWithDetails($id);

        if (!$review) $this->abort(404);

        $this->reviews->incrementViews($id);

        $userVote = null;
        if (auth()) {
            $userVote = $this->reviews->getUserVote($id, Session::get('user_id'));
        }

        // Related reviews of same product
        $related = $this->reviews->getByProduct($review['product_id']);
        $related = array_filter($related, fn($r) => $r['id'] !== $id);

        $this->view('reviews.show', [
            'title'    => $review['title'] . ' — ReelProof',
            'review'   => $review,
            'userVote' => $userVote,
            'related'  => array_slice($related, 0, 4),
        ]);
    }

    public function create(Request $request): void
    {
        $this->requireRole('reviewer');
        $products   = $this->products->getWithStats();
        $categories = (new CategoryModel())->all();

        $this->view('reviews.create', [
            'title'      => 'Post a Review',
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): void
    {
        $this->requireRole('reviewer');

        $errors = $request->validate([
            'title'      => 'required|min:10|max:120',
            'product_id' => 'required|numeric',
            'rating'     => 'required|numeric',
            'description'=> 'required|min:20',
        ]);

        $rating = (int)$request->input('rating');
        if ($rating < 1 || $rating > 5) {
            $errors['rating'][] = 'Rating must be between 1 and 5.';
        }

        // Handle video upload
        $videoFile = $request->file('video');
        $videoPath = null;
        if (!$videoFile || $videoFile['error'] !== UPLOAD_ERR_OK) {
            $errors['video'][] = 'Please upload a review video.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        $videoUpload = uploadFile(
            $videoFile,
            'uploads/videos',
            ALLOWED_VIDEO_TYPES,
            MAX_VIDEO_SIZE
        );

        if (!$videoUpload['success']) {
            Session::flash('error', $videoUpload['error']);
            $this->redirectBack();
        }

        // Handle thumbnail
        $thumbPath = null;
        $thumbFile = $request->file('thumbnail');
        if ($thumbFile && $thumbFile['error'] === UPLOAD_ERR_OK) {
            $thumbUpload = uploadFile($thumbFile, 'uploads/thumbnails', ALLOWED_IMAGE_TYPES, MAX_THUMBNAIL_SIZE);
            if ($thumbUpload['success']) {
                $thumbPath = $thumbUpload['path'];
            }
        }

        $reviewId = $this->reviews->create([
            'user_id'     => Session::get('user_id'),
            'product_id'  => (int)$request->input('product_id'),
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'video_path'  => $videoUpload['path'],
            'thumbnail'   => $thumbPath,
            'rating'      => $rating,
            'upvotes'     => 0,
            'downvotes'   => 0,
            'views'       => 0,
            'status'      => 'published',
        ]);

        // Award points to reviewer
        $userModel = new UserModel();
        $userModel->addPoints(Session::get('user_id'), 50);
        $userModel->updateCredibilityScore(Session::get('user_id'));

        Session::flash('success', 'Your review has been posted! +50 points earned.');
        $this->redirect('/reviews/' . $reviewId);
    }

    public function edit(Request $request): void
    {
        $id     = (int)$request->param('id');
        $review = $this->reviews->find($id);

        if (!$review) $this->abort(404);
        if ($review['user_id'] !== Session::get('user_id')) $this->abort(403);

        $products = $this->products->getWithStats();
        $this->view('reviews.edit', [
            'title'    => 'Edit Review',
            'review'   => $review,
            'products' => $products,
        ]);
    }

    public function update(Request $request): void
    {
        $id     = (int)$request->param('id');
        $review = $this->reviews->find($id);

        if (!$review) $this->abort(404);
        if ($review['user_id'] !== Session::get('user_id')) $this->abort(403);

        $errors = $request->validate([
            'title'       => 'required|min:10|max:120',
            'description' => 'required|min:20',
            'rating'      => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            $this->redirectBack();
        }

        $this->reviews->update($id, [
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'rating'      => (int)$request->input('rating'),
        ]);

        Session::flash('success', 'Review updated.');
        $this->redirect('/reviews/' . $id);
    }

    public function destroy(Request $request): void
    {
        $id     = (int)$request->param('id');
        $review = $this->reviews->find($id);

        if (!$review) $this->abort(404);
        if ($review['user_id'] !== Session::get('user_id')) $this->abort(403);

        $this->reviews->delete($id);
        Session::flash('success', 'Review deleted.');
        $this->redirect('/dashboard');
    }

    public function vote(Request $request): void
    {
        $id   = (int)$request->param('id');
        $type = $request->input('type');

        if (!in_array($type, ['up', 'down'])) {
            $this->json(['error' => 'Invalid vote type.'], 400);
        }

        $result = $this->reviews->vote($id, Session::get('user_id'), $type);

        // Update reviewer credibility
        $review    = $this->reviews->find($id);
        $userModel = new UserModel();
        $userModel->updateCredibilityScore($review['user_id']);

        // Award/deduct points
        if ($result['action'] === 'added' && $type === 'up') {
            $userModel->addPoints($review['user_id'], 10);
        }

        // Get updated counts
        $updated = $this->reviews->find($id);
        $this->json([
            'action'    => $result['action'],
            'type'      => $type,
            'upvotes'   => (int)$updated['upvotes'],
            'downvotes' => (int)$updated['downvotes'],
        ]);
    }
}
