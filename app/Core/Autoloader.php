<?php

class Autoloader
{
    private static array $namespaceMap = [
        'Controllers' => APP_PATH . '/Controllers/',
        'Models'      => APP_PATH . '/Models/',
        'Core'        => APP_PATH . '/Core/',
        'Middleware'  => APP_PATH . '/Middleware/',
        'Helpers'     => APP_PATH . '/Helpers/',
    ];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function load(string $class): void
    {
        foreach (self::$namespaceMap as $namespace => $path) {
            if (str_starts_with($class, $namespace) || array_key_exists($class, self::getClasses())) {
                $file = $path . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }

        // Flat class search across all paths
        foreach (self::$namespaceMap as $path) {
            $file = $path . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }

    private static function getClasses(): array
    {
        return [];
    }
}

Autoloader::register();
