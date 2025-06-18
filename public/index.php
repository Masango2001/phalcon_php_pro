<?php

declare(strict_types=1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;
use Phalcon\Http\Response;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

try {
    // Initialiser le conteneur de services
    $di = new FactoryDefault();

    // Charger la config
    $config = include APP_PATH . "/config/config.php";
    $di->setShared('config', $config);

    // Autoloader + services
    include APP_PATH . '/config/loader.php';
    include APP_PATH . '/config/services.php';

    // Router
    $di->setShared('router', function () {
        $router = include APP_PATH . '/config/router.php';
        $router->setDefaultNamespace('GestionStockVente\\Controllers');

        $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
        return $router;
    });

    // Lancer l'application
    $application = new Application($di);

    $response = $application->handle($_SERVER['REQUEST_URI']);
    $response->send();
} catch (\Throwable $e) {
    // En cas d'erreur
    if (isset($di) && $di->has('config') && $di->getShared('config')->debug->enabled) {
        echo '<h1>Erreur de l\'application Phalcon</h1>';
        echo '<p><strong>Message :</strong> ' . $e->getMessage() . '</p>';
        echo '<p><strong>Fichier :</strong> ' . $e->getFile() . ' (Ligne: ' . $e->getLine() . ')</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        $response = new Response();
        $response->redirect('/error/show500', true, 500);
        $response->send();
    }
}
