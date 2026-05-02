<?php

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
