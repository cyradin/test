<?php
require('../vendor/autoload.php');

use Lib\Config;

/**
 * Class autoload function
 */
spl_autoload_register(function($class) {
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
    $action = $parameters['action'] . 'Action';
}

if (! ($controller && $action)) {
    http_response_code(404);
    die();
}

/**
 * Setting up Twig
 */
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig = new Twig_Environment($loader);

$controllerInstance = new $controller($twig);
$controllerInstance->$action();