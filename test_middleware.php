<?php

require_once __DIR__ . '/../app/Core/Autoloader.php';

$autoloader = new Autoloader();
$autoloader->register();

echo "Testing middleware autoloading...\n";

// Test AuthMiddleware
if (class_exists('AuthMiddleware')) {
    echo "✅ AuthMiddleware found\n";
} else {
    echo "❌ AuthMiddleware not found\n";
}

// Test GuestMiddleware
if (class_exists('GuestMiddleware')) {
    echo "✅ GuestMiddleware found\n";
} else {
    echo "❌ GuestMiddleware not found\n";
}

// Test BrandMiddleware
if (class_exists('BrandMiddleware')) {
    echo "✅ BrandMiddleware found\n";
} else {
    echo "❌ BrandMiddleware not found\n";
}

echo "Test complete.\n";