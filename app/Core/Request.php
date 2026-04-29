<?php

class Request
{
    private array $params = [];

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        // Strip base path if running in a subdirectory
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? '');
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        return '/' . trim($uri, '/') ?: '/';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $this->params[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST, $this->params);
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function json(): ?array
    {
        $body = file_get_contents('php://input');
        return $body ? json_decode($body, true) : null;
    }

    public function validate(array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $value = $this->input($field);
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
                    }
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Please enter a valid email address.';
                }
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = ucfirst($field) . ' must be a number.';
                }
            }
        }
        return $errors;
    }
}
