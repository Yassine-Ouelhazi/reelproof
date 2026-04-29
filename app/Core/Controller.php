<?php

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        // Extract data as variables
        extract($data);

        // Convert dot notation to path: 'auth.login' → 'auth/login'
        $viewPath = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new RuntimeException("View [{$view}] not found at {$viewPath}");
        }

        require $viewPath;
    }

    protected function redirect(string $url): never
    {
        Response::redirect($url);
    }

    protected function redirectBack(): never
    {
        Response::redirectBack();
    }

    protected function json(array $data, int $status = 200): never
    {
        Response::json($data, $status);
    }

    protected function abort(int $code = 404): never
    {
        Response::abort($code);
    }

    protected function isAuthenticated(): bool
    {
        return Session::has('user_id');
    }

    protected function currentUser(): ?array
    {
        if (!$this->isAuthenticated()) return null;
        $userModel = new UserModel();
        return $userModel->find(Session::get('user_id'));
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            Session::flash('error', 'Please login to continue.');
            $this->redirect('/login');
        }
    }

    protected function requireRole(string $role): void
    {
        $this->requireAuth();
        if (Session::get('user_role') !== $role) {
            $this->abort(403);
        }
    }
}
