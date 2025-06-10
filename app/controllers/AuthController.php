use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function loginAction()
    {
        // Si déjà connecté, rediriger selon le rôle
        if ($this->session->has('user_id')) {
            return $this->response->redirect('dashboard');
        }
        // Affiche login.volt
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function authenticateAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username', 'string');
            $password = $this->request->getPost('password', 'string');

            $user = Utilisateur::findFirst([
                'conditions' => 'username = :username:',
                'bind' => ['username' => $username]
            ]);

            if ($user && $user->verifyPassword($password)) {
                $user->login();
                // Redirection selon le rôle
                switch ($user->role) {
                    case 'admin':
                        return $this->response->redirect('admin/dashboard');
                    case 'vendeur':
                        return $this->response->redirect('vendeur/dashboard');
                    case 'magasinier':
                        return $this->response->redirect('magasinier/dashboard');
                    default:
                        return $this->response->redirect('dashboard');
                }
            } else {
                $this->flashSession->error('Nom d\'utilisateur ou mot de passe incorrect.');
                return $this->response->redirect('auth/login');
            }
        }
        return $this->response->redirect('auth/login');
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logoutAction()
    {
        if ($this->session->has('user_id')) {
            $user = Utilisateur::findFirst($this->session->get('user_id'));
            if ($user) {
                $user->logout();
            } else {
                $this->session->destroy();
            }
        }
        return $this->response->redirect('auth/login');
    }
}