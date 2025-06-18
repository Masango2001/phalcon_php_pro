<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\View;
use GestionStockVente\Models\Utilisateur;

class AuthController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Authentification');
    }

    /**
     * Affiche la page de login sans layout principal
     */
    public function loginAction()
    {
        // Si déjà connecté, rediriger
        if ($this->session->has('auth')) {
            return $this->response->redirect('/dashboard');
        }

        // Pour ne pas utiliser le layout principal lors du login
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        // Charger la vue de login
        $this->view->pick('auth/index'); // Assurez-vous que c'est le bon chemin de vue
    }

    /**
     * Gère la soumission du formulaire de login
     */
    public function authAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username', 'string');
            $password = $this->request->getPost('password', 'string');

            if (empty($username) || empty($password)) {
                $this->flashSession->error('Veuillez remplir tous les champs.');
                return $this->response->redirect('/login');
            }

            $user = \GestionStockVente\Models\Utilisateur::findFirst([
                'conditions' => 'USERNAME = :username:',
                'bind' => ['username' => $username]
            ]);

            if ($user && password_verify($password, $user->PASSWORD)) {
                $this->session->set('auth', [
                    'id' => $user->ID_UTILISATEUR,
                    'username' => $user->USERNAME,
                    'role' => strtolower($user->ROLE) // Forcer le rôle en minuscule
                ]);

                $this->session->set('lastLogin', date('Y-m-d H:i:s'));

                return $this->response->redirect('/dashboard');
            } else {
                $this->flashSession->error('Nom d\'utilisateur ou mot de passe incorrect.');
                return $this->response->redirect('/login');
            }
        } else {
            $this->flashSession->error('Méthode non autorisée. Veuillez utiliser le formulaire de connexion.');
            return $this->response->redirect('/login');
        }
    }
    public function logoutAction()
    {
        if ($this->session->has('auth')) {
            $this->session->destroy();
            // Réinitialiser la session pour éviter les résidus
            $this->session->start();
            $this->flashSession->success('Vous avez été déconnecté avec succès.');
        } else {
            $this->flashSession->notice('Aucune session active à déconnecter.');
        }
        return $this->response->redirect('/login');
    }
}
