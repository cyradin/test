<?php
require '../vendor/autoload.php';

use Lib\Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class autoload function
 */
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', DIRECTORY_SEPARATOR, $class . '.php');
    if (file_exists($file)) {
        require($file);
    }
});

$config = Config::instance();

/**
 * Matching routes
 */
$routes = $config->get('routes');
foreach ($routes as $route => $parameters) {
    if ($route != $_SERVER['REDIRECT_URL']) {
        continue;
    }

    $controller = 'Controllers\\' . $parameters['controller'] . 'Controller';
    $action     = $parameters['action'] . 'Action';
}

if (!($controller && $action)) {
    $error = 404;
}

$db = Lib\Database::instance();
if (!$db->isConnected()) {
    $error = 500;
}

/**
 * Setting up Twig
 */
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig   = new Twig_Environment($loader);

if (! $error) {
    $controllerInstance = new $controller($twig);
    $controllerInstance->$action(Request::createFromGlobals());
} else {
    $controllerInstance = new Controllers\ErrorController($twig);
    $controllerInstance->sendError($error);
}