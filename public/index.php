<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', __DIR__);

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/app/Core/Autoloader.php';
require_once ROOT_PATH . '/app/Core/Database.php';
require_once ROOT_PATH . '/app/Core/Router.php';
require_once ROOT_PATH . '/app/Core/Request.php';
require_once ROOT_PATH . '/app/Core/Response.php';
require_once ROOT_PATH . '/app/Core/Session.php';
require_once ROOT_PATH . '/app/Helpers/helpers.php';

// Boot session
Session::start();

// Load routes
$router = new Router();
require_once ROOT_PATH . '/routes/web.php';

// Dispatch
$request = new Request();
$router->dispatch($request);
