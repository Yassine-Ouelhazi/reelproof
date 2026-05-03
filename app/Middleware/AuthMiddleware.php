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
