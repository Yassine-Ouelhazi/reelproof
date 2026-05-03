<?php

class BrandMiddleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id') || Session::get('user_role') !== 'brand') {
            Response::abort(403);
        }
    }
}