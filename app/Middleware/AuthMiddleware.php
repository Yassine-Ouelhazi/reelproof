<?php

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please login to continue.');
            Response::redirect('/login');
        }
    }
}

class GuestMiddleware
{
    public function handle(Request $request): void
    {
        if (Session::has('user_id')) {
            Response::redirect('/dashboard');
        }
    }
}

class BrandMiddleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'brand') {
            Response::abort(403);
        }
    }
}
