<?php

class Response
{
    public static function redirect(string $url): never
    {
        header('Location: ' . APP_URL . $url);
        exit;
    }

    public static function redirectBack(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
        header('Location: ' . $referer);
        exit;
    }

    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function abort(int $code = 404): never
    {
        http_response_code($code);
        $view = VIEW_PATH . "/errors/{$code}.php";
        if (file_exists($view)) {
            require $view;
        } else {
            echo "Error {$code}";
        }
        exit;
    }
}
