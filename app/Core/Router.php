<?php

class Router
{
    private array $routes = [];

    // ── Route Registration ────────────────────────────────────────────────────

    public function get(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, string|array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, string|array $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
            'pattern'    => $this->pathToPattern($path),
        ];
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        // Support method override via POST _method field
        if ($method === 'POST' && $request->input('_method')) {
            $method = strtoupper($request->input('_method'));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Run middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    // Ensure middleware class exists before instantiation
                    if (!class_exists($middlewareClass)) {
                        throw new RuntimeException(
                            "Middleware '{$middlewareClass}' not found. " .
                            "Expected file: app/Middleware/{$middlewareClass}.php"
                        );
                    }
                    $mw = new $middlewareClass();
                    $mw->handle($request);
                }

                // Resolve handler
                $this->resolveHandler($route['handler'], $request);
                return;
            }
        }

        // No route matched
        $this->notFound();
    }

    private function resolveHandler(string|array $handler, Request $request): void
    {
        if (is_string($handler)) {
            // 'HomeController@index'
            [$class, $method] = explode('@', $handler);
        } else {
            [$class, $method] = $handler;
        }

        if (!class_exists($class)) {
            throw new RuntimeException(
                "Controller '{$class}' not found. " .
                "Expected file: app/Controllers/{$class}.php"
            );
        }

        if (!method_exists($class, $method)) {
            throw new RuntimeException(
                "Method '{$method}' not found in controller '{$class}'."
            );
        }

        $controller = new $class();
        $controller->$method($request);
    }

    private function pathToPattern(string $path): string
    {
        // Convert /reviews/{id} → named capture group regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function notFound(): void
    {
        http_response_code(404);
        require_once VIEW_PATH . '/errors/404.php';
        exit;
    }
}
