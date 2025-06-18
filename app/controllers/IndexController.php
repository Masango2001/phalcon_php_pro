<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;

// Assurez-vous que IndexController hérite de ControllerBase
class IndexController extends ControllerBase
{

    public function show404Action()
    {
        $this->response->setStatusCode(404, 'Not Found');
        $this->view->pick('errors/404');
    }
}
