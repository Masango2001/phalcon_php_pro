<?php

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use GestionStockVente\Models\Utilisateur;

class UsersController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Utilisateurs');
    }

    private function getValidRoles(): array
    {
        return ['Admin', 'Vendeur', 'Magasinier'];
    }

    public function indexAction()
    {
        // Page d'entrée pour la gestion des utilisateurs
        $this->view->pick('users/index');
    }

    public function listUsersAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            $users = Utilisateur::find(['order' => 'USERNAME ASC']);
            $usersArray = array_map(function ($user) {
                $data = (array) $user;
                unset($data['PASSWORD']);
                return $data;
            }, $users->toArray());

            return $response->setJsonContent([
                'success' => true,
                'message' => 'Liste des utilisateurs récupérée avec succès.',
                'data' => $usersArray
            ]);
        } catch (\Throwable $e) {
            error_log('Erreur listUsersAction: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $response->setJsonContent([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }

    public function createAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            if (!$this->request->isPost()) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Méthode non autorisée.'
                ])->setStatusCode(405, 'Method Not Allowed');
            }

            $rawBody = $this->request->getRawBody();
            $jsonData = $rawBody ? json_decode($rawBody, true) : [];

            $username = $jsonData['username'] ?? null;
            $email = $jsonData['email'] ?? null;
            $password = $jsonData['password'] ?? null;
            $role = $jsonData['role'] ?? null;

            error_log('Données reçues pour création utilisateur: ' . print_r($jsonData, true));

            if (empty($username) || empty($email) || empty($password) || empty($role)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Nom d\'utilisateur, email, mot de passe et rôle sont requis.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Format d\'email invalide.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if (!in_array($role, $this->getValidRoles())) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Rôle invalide.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if (Utilisateur::findFirstByEmail($email)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Un utilisateur avec cet email existe déjà.'
                ])->setStatusCode(409, 'Conflict');
            }

            if (Utilisateur::findFirstByUsername($username)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Un utilisateur avec ce nom d\'utilisateur existe déjà.'
                ])->setStatusCode(409, 'Conflict');
            }

            $user = new Utilisateur();
            $user->USERNAME = $username;
            $user->EMAIL = $email;
            $user->PASSWORD = password_hash($password, PASSWORD_DEFAULT);
            $user->ROLE = $role;

            if ($user->save()) {
                error_log('Utilisateur créé: ID=' . $user->ID_UTILISATEUR);
                $userData = $user->toArray();
                unset($userData['PASSWORD']);
                return $response->setJsonContent([
                    'success' => true,
                    'message' => 'Utilisateur créé avec succès !',
                    'data' => $userData
                ])->setStatusCode(201, 'Created');
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $user->getMessages());
                error_log('Erreur sauvegarde utilisateur: ' . implode(', ', $messages));
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Échec de la création de l\'utilisateur: ' . implode(', ', $messages)
                ])->setStatusCode(400, 'Bad Request');
            }
        } catch (\Throwable $e) {
            error_log('Exception dans createAction: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $response->setJsonContent([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }

    public function updateAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            if (!$this->request->isPost()) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Méthode non autorisée.'
                ])->setStatusCode(405, 'Method Not Allowed');
            }

            $rawBody = $this->request->getRawBody();
            $jsonData = $rawBody ? json_decode($rawBody, true) : [];

            $userId = $jsonData['id_utilisateur'] ?? null;
            $username = $jsonData['username'] ?? null;
            $email = $jsonData['email'] ?? null;
            $role = $jsonData['role'] ?? null;
            $password = $jsonData['password'] ?? null;

            error_log('Données reçues pour modification utilisateur: ' . print_r($jsonData, true));

            if (empty($userId) || empty($username) || empty($email) || empty($role)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'ID utilisateur, nom d\'utilisateur, email et rôle sont requis.'
                ])->setStatusCode(400, 'Bad Request');
            }

            $user = Utilisateur::findFirst($userId);
            if (!$user) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ])->setStatusCode(404, 'Not Found');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Format d\'email invalide.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if (!in_array($role, $this->getValidRoles())) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Rôle invalide.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if ($email !== $user->EMAIL && Utilisateur::findFirstByEmail($email)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé.'
                ])->setStatusCode(409, 'Conflict');
            }

            if ($username !== $user->USERNAME && Utilisateur::findFirstByUsername($username)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà pris.'
                ])->setStatusCode(409, 'Conflict');
            }

            $user->USERNAME = $username;
            $user->EMAIL = $email;
            $user->ROLE = $role;
            if (!empty($password)) {
                $user->PASSWORD = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($user->save()) {
                error_log('Utilisateur modifié: ID=' . $user->ID_UTILISATEUR);
                $userData = $user->toArray();
                unset($userData['PASSWORD']);
                return $response->setJsonContent([
                    'success' => true,
                    'message' => 'Utilisateur mis à jour avec succès !',
                    'data' => $userData
                ]);
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $user->getMessages());
                error_log('Erreur sauvegarde utilisateur: ' . implode(', ', $messages));
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Échec de la mise à jour de l\'utilisateur: ' . implode(', ', $messages)
                ])->setStatusCode(400, 'Bad Request');
            }
        } catch (\Throwable $e) {
            error_log('Exception dans updateAction: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $response->setJsonContent([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }

    public function deleteAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            if (!$this->request->isPost()) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Méthode non autorisée.'
                ])->setStatusCode(405, 'Method Not Allowed');
            }

            $rawBody = $this->request->getRawBody();
            $jsonData = $rawBody ? json_decode($rawBody, true) : [];

            $userId = $jsonData['id_utilisateur'] ?? null;

            error_log('Données reçues pour suppression utilisateur: ' . print_r($jsonData, true));

            if (empty($userId)) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'ID utilisateur manquant.'
                ])->setStatusCode(400, 'Bad Request');
            }

            $user = Utilisateur::findFirst($userId);
            if (!$user) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ])->setStatusCode(404, 'Not Found');
            }

            // Vérifier si l'utilisateur a des ventes associées
            $ventesCount = $user->Ventes->count();
            if ($ventesCount > 0) {
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Impossible de supprimer cet utilisateur car il a des ventes associées.'
                ])->setStatusCode(400, 'Bad Request');
            }

            if ($user->delete()) {
                error_log('Utilisateur supprimé: ID=' . $userId);
                return $response->setJsonContent([
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès !'
                ]);
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $user->getMessages());
                error_log('Erreur suppression utilisateur: ' . implode(', ', $messages));
                return $response->setJsonContent([
                    'success' => false,
                    'message' => 'Échec de la suppression de l\'utilisateur: ' . implode(', ', $messages)
                ])->setStatusCode(400, 'Bad Request');
            }
        } catch (\Throwable $e) {
            error_log('Exception dans deleteAction: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $response->setJsonContent([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }
}
