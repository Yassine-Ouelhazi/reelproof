<?php

class AuthController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function loginForm(Request $request): void
    {
        $this->view('auth.login', ['title' => 'Login']);
    }

    public function login(Request $request): void
    {
        $errors = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        $user = $this->users->findByEmail($request->input('email'));

        if (!$user || !$this->users->verifyPassword($request->input('password'), $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        // Set session
        Session::set('user_id',   $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('username',  $user['username']);

        Session::flash('success', 'Welcome back, ' . e($user['username']) . '!');
        $this->redirect('/dashboard');
    }

    public function registerForm(Request $request): void
    {
        $this->view('auth.register', ['title' => 'Create Account']);
    }

    public function register(Request $request): void
    {
        $errors = $request->validate([
            'username' => 'required|min:3|max:30',
            'email'    => 'required|email',
            'password' => 'required|min:8',
            'role'     => 'required',
        ]);

        if (!in_array($request->input('role'), ['reviewer', 'buyer', 'brand'])) {
            $errors['role'][] = 'Invalid account type.';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        if ($this->users->findByEmail($request->input('email'))) {
            Session::flash('error', 'This email is already registered.');
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        if ($this->users->findByUsername($request->input('username'))) {
            Session::flash('error', 'This username is already taken.');
            Session::flash('old', $request->all());
            $this->redirectBack();
        }

        $userId = $this->users->createUser([
            'username'    => $request->input('username'),
            'email'       => $request->input('email'),
            'password'    => $request->input('password'),
            'role'        => $request->input('role'),
            'full_name'   => $request->input('full_name', ''),
            'bio'         => '',
            'avatar'      => 'avatars/default.png',
        ]);

        // If brand role, create brand profile
        if ($request->input('role') === 'brand') {
            $brandModel = new BrandModel();
            $brandModel->createWithSlug([
                'user_id'     => $userId,
                'name'        => $request->input('brand_name', $request->input('username')),
                'description' => '',
                'logo'        => 'uploads/avatars/default.png',
                'website'     => '',
            ]);
        }

        Session::set('user_id',   $userId);
        Session::set('user_role', $request->input('role'));
        Session::set('username',  $request->input('username'));

        Session::flash('success', 'Account created! Welcome to ReelProof.');
        $this->redirect('/dashboard');
    }

    public function logout(Request $request): void
    {
        Session::destroy();
        $this->redirect('/');
    }
}
