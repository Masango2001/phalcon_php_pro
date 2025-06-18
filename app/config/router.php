<?php
// app/config/router.php

use Phalcon\Mvc\Router;

$router = new Router(false);

$router->setDefaultController('index');
$router->setDefaultAction('index');

$router->add('/', [
    'controller' => 'dashboard',
    'action'     => 'index',
])->setName('dashboard');

$router->add('/test-db', [
    'controller' => 'index',
    'action'     => 'testDb',
])->setName('test-db');

$router->add('/dashboard', [
    'controller' => 'dashboard',
    'action'     => 'index',
])->setName('dashboard');

$router->add('/products', [
    'controller' => 'products',
    'action'     => 'index',
]);
$router->add('/products/create', [
    'controller' => 'products',
    'action'     => 'create',
]);
$router->add('/products/edit/{id}', [
    'controller' => 'products',
    'action'     => 'edit',
]);
$router->add('/products/delete/{id}', [
    'controller' => 'products',
    'action'     => 'delete',
]);
$router->add('/products/view/{id}', [
    'controller' => 'products',
    'action'     => 'view',
]);
$router->add('/stocks', [
    'controller' => 'stocks',
    'action'     => 'index',
]);
$router->add('/stocks/create', [
    'controller' => 'stocks',
    'action'     => 'create',
]);
$router->add('/stocks/edit/{id}', [
    'controller' => 'stocks',
    'action'     => 'edit',
]);
$router->add('/stocks/delete/{id}', [
    'controller' => 'stocks',
    'action'     => 'delete',
]);
$router->add('/stocks/view/{id}', [
    'controller' => 'stocks',
    'action'     => 'view',
]);
$router->add('/sales', [
    'controller' => 'ventes',
    'action'     => 'index',
])->setName('sales');
$router->add('/sales/view/{id:[0-9]+}', [
    'controller' => 'ventes',
    'action'     => 'view',
])->setName('sales-view');

$router->add('/clients', [
    'controller' => 'clients',
    'action'     => 'index',
])->setName('clients');
$router->add('/clients/view/{id:[0-9]+}', [
    'controller' => 'clients',
    'action'     => 'view'
])->setName('clients-view');
$router->add('/clients/add', [
    'controller' => 'clients',
    'action'     => 'add',
])->setName('clients-add');
$router->add('/clients/edit/{id:[0-9]+}', [
    'controller' => 'clients',
    'action'     => 'edit'
])->setName('clients-edit');

$router->add('/clients/delete/{id:[0-9]+}', [
    'controller' => 'clients',
    'action'     => 'delete'
])->setName('clients-delete');
$router->add('/suppliers', [
    'controller' => 'suppliers',
    'action'     => 'index',
])->setName('suppliers');

$router->add('/suppliers/add', [
    'controller' => 'suppliers',
    'action'     => 'add',
])->setName('suppliers-add');

$router->add('/suppliers/view/{id:[0-9]+}', [
    'controller' => 'suppliers',
    'action'     => 'view'
])->setName('suppliers-view');

$router->add('/suppliers/edit/{id:[0-9]+}', [
    'controller' => 'suppliers',
    'action'     => 'edit'
])->setName('suppliers-edit');

$router->add('/suppliers/delete/{id:[0-9]+}', [
    'controller' => 'suppliers',
    'action'     => 'delete'
])->setName('suppliers-delete');
$router->add('/reports', [
    'controller' => 'reports',
    'action'     => 'index',
])->setName('reports');
$router->add('/reports/get-data', [
    'controller' => 'reports',
    'action'     => 'getReportData',
])->setName('get-report-data');

$router->add('/users', [
    'controller' => 'users',
    'action'     => 'index',
])->setName('users-management-page');
$router->add('/users/list', [
    'controller' => 'users',
    'action' => 'listUsers',
])->setName('users-list');

$router->add('/users/create', [
    'controller' => 'users',
    'action' => 'create',
])->setName('users-create');

$router->add('/users/update', [
    'controller' => 'users',
    'action' => 'update',
])->setName('users-update');

$router->add('/users/delete', [
    'controller' => 'users',
    'action' => 'delete',
])->setName('users-delete');

$router->add('/login', [
    'controller' => 'auth',
    'action'     => 'login',
])->setName('login');

$router->addPost('/login', [
    'controller' => 'auth',
    'action'     => 'auth',
])->setName('login-post');

$router->add('/logout', [
    'controller' => 'auth',
    'action'     => 'logout',
])->setName('logout');
$router->notFound([
    'controller' => 'index',
    'action'     => 'show404',
]);

return $router;
