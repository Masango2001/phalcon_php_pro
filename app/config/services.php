<?php

declare(strict_types=1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Html\Escaper;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Tag;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . '/config/config.php';
});

/**
 * URL Resolver
 */
$di->setShared('url', function () {
    $config = $this->getConfig();
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

/**
 * View component with Volt and PhpEngine
 */
$di->setShared('view', function () {
    $config = $this->getConfig();
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);
    $view->setLayoutsDir('layouts/');
    $view->setLayout('main');

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();
            $volt = new VoltEngine($view, $this);
            $volt->setOptions([
                'path'      => $config->application->cacheDir,
                'extension' => '.php',
                'prefix'    => '__volt__',
            ]);
            return $volt;
        },
        '.phtml' => PhpEngine::class
    ]);

    return $view;
});

/**
 * Database connection
 */
$di->setShared('db', function () {
    $config = $this->getConfig();
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if (isset($config->database->port)) {
        $params['port'] = $config->database->port;
    }

    return new DbAdapter($params);
});

/**
 * Metadata (for models)
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Flash messages using session (recommended)
 */
$di->setShared('flash', function () {
    $escaper = new Escaper();
    $flash = new FlashSession($escaper);
    $flash->setImplicitFlush(false); // important pour éviter l'affichage immédiat
    $flash->setAutomaticHtml(false); // désactive le HTML automatique de Phalcon
    return $flash;
});

/**
 * Session management
 */
$di->setShared('session', function () {
    $manager = new SessionManager();
    $files = new SessionAdapter([
        'savePath' => sys_get_temp_dir(),
    ]);
    $manager->setAdapter($files);
    $manager->start();
    return $manager;
});

/**
 * Tag helper
 */
$di->setShared('tag', function () {
    return new Tag();
});
