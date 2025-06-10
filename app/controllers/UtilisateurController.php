use Phalcon\Mvc\Controller;

<?php


class UtilisateurController extends Controller
{
    // Vérifie si l'utilisateur est admin
    private function isAdmin()
    {
        return $this->session->has('auth') && $this->session->get('auth')['role'] === 'admin';
    }

    // Liste tous les utilisateurs
    public function indexAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
        $utilisateurs = Utilisateur::find();
        $this->view->utilisateurs = $utilisateurs;
    }

    // Affiche le formulaire de création
    public function newAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
    }

    // Crée un nouvel utilisateur
    public function createAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
        if ($this->request->isPost()) {
            $utilisateur = new Utilisateur();
            $utilisateur->username = $this->request->getPost('username', 'string');
            $utilisateur->password = $this->request->getPost('password', 'string');
            $utilisateur->email = $this->request->getPost('email', 'email');
            $utilisateur->role = $this->request->getPost('role', 'string');

            if ($utilisateur->save()) {
                $this->flashSession->success("Utilisateur créé avec succès.");
                return $this->response->redirect('utilisateur/index');
            } else {
                $messages = $utilisateur->getMessages();
                foreach ($messages as $message) {
                    $this->flashSession->error($message);
                }
            }
        }
    }

    // Affiche le formulaire d'édition
    public function editAction($id)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
        $utilisateur = Utilisateur::findFirstById_utilisateur($id);
        if (!$utilisateur) {
            $this->flashSession->error("Utilisateur non trouvé.");
            return $this->response->redirect('utilisateur/index');
        }
        $this->view->utilisateur = $utilisateur;
    }

    // Met à jour un utilisateur
    public function updateAction($id)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
        if ($this->request->isPost()) {
            $utilisateur = Utilisateur::findFirstById_utilisateur($id);
            if (!$utilisateur) {
                $this->flashSession->error("Utilisateur non trouvé.");
                return $this->response->redirect('utilisateur/index');
            }
            $utilisateur->username = $this->request->getPost('username', 'string');
            $utilisateur->email = $this->request->getPost('email', 'email');
            $utilisateur->role = $this->request->getPost('role', 'string');
            $password = $this->request->getPost('password', 'string');
            if (!empty($password)) {
                $utilisateur->password = $password;
            }

            if ($utilisateur->save()) {
                $this->flashSession->success("Utilisateur mis à jour.");
                return $this->response->redirect('utilisateur/index');
            } else {
                $messages = $utilisateur->getMessages();
                foreach ($messages as $message) {
                    $this->flashSession->error($message);
                }
            }
        }
    }

    // Supprime un utilisateur
    public function deleteAction($id)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('index/index');
        }
        $utilisateur = Utilisateur::findFirstById_utilisateur($id);
        if ($utilisateur && $utilisateur->delete()) {
            $this->flashSession->success("Utilisateur supprimé.");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('utilisateur/index');
    }
}