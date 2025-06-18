<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class ControllerBase extends Controller
{
    public function initialize()
    {

        $this->flashSession->setCssClasses([
            'error'   => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice'  => 'alert alert-info',
            'warning' => 'alert alert-warning'
        ]);

        $this->view->setLayout('main');

        if ($this->request->isAjax()) {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }

        // Sécurisation des routes
        $publicActions = ['login', 'auth', 'logout'];
        $currentController = strtolower($this->router->getControllerName());
        $currentAction = strtolower($this->router->getActionName());

        if (!$this->session->has('auth') && !($currentController === 'auth' && in_array($currentAction, $publicActions))) {
            $this->flash->error('Vous devez vous connecter pour accéder à cette page.');
            return $this->response->redirect('/login');
        }
    }
}
