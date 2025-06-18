<?php

use Phalcon\Autoload\Loader;

// Récupération de la configuration
$config = include APP_PATH . '/config/config.php';

$loader = new Loader();

// Enregistrement des namespaces pour les contrôleurs et les modèles
$loader->setNamespaces([
    'GestionStockVente\Controllers' => $config->application->controllersDir,
    'GestionStockVente\Models'      => $config->application->modelsDir,
]);

// Tu peux aussi charger des classes sans namespace si tu en as besoin
$loader->setDirectories([
    $config->application->controllersDir,
    $config->application->modelsDir,
]);

// Enregistre l’autoloader
$loader->register();
