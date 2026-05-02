<?php

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
