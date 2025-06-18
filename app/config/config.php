<?php

declare(strict_types=1);

use Phalcon\Config\Config;

/*
 * Définition des chemins de base pour l'application.
 * Assure que APP_PATH et BASE_PATH sont correctement définis, peu importe l'environnement (web ou CLI).
 */

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new Config([
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => '/',

    ],
    'database' => [
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'stocketvente',
        'charset'  => 'utf8mb4',
    ],
    'debug' => [
        'enabled' => true,
    ],
    'phalcon' => [
        'session' => [
            'adapter' => 'files',
            'options' => [
                'uniqueId' => 'gestionstockvente_session'
            ]
        ]
    ],
]);
