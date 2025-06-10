<?php
use Phalcon\Mvc\Model;

class Utilisateur extends Model
{
    public $id_utilisateur;
    public $username;
    public $password;
    public $email;
    public $role;

    public function initialize()
    {
        $this->setSource('utilisateurs');
    }

    public function beforeSave()
    {
        if ($this->password && !$this->isPasswordHashed()) {
            $security = $this->getDI()->getShared('security');
            $this->password = $security->hash($this->password);
        }
    }

    private function isPasswordHashed()
    {
        return strlen($this->password) === 60 && substr($this->password, 0, 4) === '$2y$';
    }

    public function verifyPassword($password)
    {
        $security = $this->getDI()->getShared('security');
        return $security->checkHash($password, $this->password);
    }

    // Méthodes communes à tous les utilisateurs
    public function login()
    {
        // Logique de connexion commune
        $session = $this->getDI()->getShared('session');
        $session->set('user_id', $this->id_utilisateur);
        $session->set('user_role', $this->role);
        $session->set('username', $this->username);
        return true;
    }

    public function logout()
    {
        $session = $this->getDI()->getShared('session');
        $session->destroy();
        return true;
    }

    public function getSource()
    {
        return 'utilisateurs';
    }
}