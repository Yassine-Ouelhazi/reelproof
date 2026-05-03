<?php

class GuestMiddleware
{
    public function handle(Request $request): void
    {
        if (Session::has('user_id')) {
            Response::redirect('/dashboard');
        }
    }
}