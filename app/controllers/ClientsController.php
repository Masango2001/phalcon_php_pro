<?php
// app/controllers/ClientsController.php

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Client;
use GestionStockVente\Models\Vente;
use GestionStockVente\Models\Concerner;

class ClientsController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Clients');
    }

    public function indexAction()
    {
        $searchQuery = $this->request->getQuery('search', 'string', '');
        $clientsData = [];

        $findParams = [
            "order" => "NOM_CLIENT ASC, PRENOM_CLIENT ASC"
        ];

        if (!empty($searchQuery)) {
            $findParams['conditions'] = "(NOM_CLIENT LIKE :search_query: OR PRENOM_CLIENT LIKE :search_query: OR TELEPHONE_CLIENT LIKE :search_query: OR ADRESSE_CLIENT LIKE :search_query:)";
            $findParams['bind'] = ['search_query' => '%' . $searchQuery . '%'];
        }

        $allClients = Client::find($findParams);

        foreach ($allClients as $client) {
            $totalSpent = 0;
            $purchaseCount = 0;

            $builder = $this->modelsManager->createBuilder()
                ->columns('SUM(co.QUANTITE_VENDUE * co.PRIX_UNITAIRE_VENDUE) as total_spent, COUNT(DISTINCT v.ID_VENTE) as purchase_count')
                ->from(['v' => Vente::class])
                ->join(Concerner::class, 'v.ID_VENTE = co.ID_VENTE', 'co')
                ->where("v.ID_CLIENT = :id_client:", ['id_client' => $client->ID_CLIENT]);

            $result = $builder->getQuery()->getSingleResult();

            if ($result) {
                $totalSpent = (float)$result->total_spent;
                $purchaseCount = (int)$result->purchase_count;
            }

            $clientsData[] = [
                'id' => $client->ID_CLIENT,
                'name' => $client->NOM_CLIENT . ' ' . $client->PRENOM_CLIENT,
                'phone' => $client->TELEPHONE_CLIENT ?: 'N/A',
                'adresse' => $client->ADRESSE_CLIENT ?: 'N/A',
                'totalSpent' => $totalSpent,
                'purchaseCount' => $purchaseCount
            ];
        }

        $this->view->setVars([
            'clients' => $clientsData,
            'searchQuery' => $searchQuery,
            'canViewClientsList' => true,
            'canModifyClients' => true,
            'canDeleteClients' => true
        ]);
        $this->view->pick("clients/index");
    }

    public function viewAction($id)
    {
        $client = Client::findFirst($id);
        if (!$client) {
            $this->flash->error("Client non trouvé.");
            return $this->response->redirect('/clients');
        }

        $totalSpent = 0;
        $purchaseCount = 0;
        $purchases = [];

        $builder = $this->modelsManager->createBuilder()
            ->columns('SUM(co.QUANTITE_VENDUE * co.PRIX_UNITAIRE_VENDUE) as total_spent, COUNT(DISTINCT v.ID_VENTE) as purchase_count')
            ->from(['v' => Vente::class])
            ->join(Concerner::class, 'v.ID_VENTE = co.ID_VENTE', 'co')
            ->where("v.ID_CLIENT = :id_client:", ['id_client' => $client->ID_CLIENT]);

        $result = $builder->getQuery()->getSingleResult();
        if ($result) {
            $totalSpent = (float)$result->total_spent;
            $purchaseCount = (int)$result->purchase_count;
        }

        $ventes = Vente::find([
            "conditions" => "ID_CLIENT = :id_client:",
            "bind" => ["id_client" => $client->ID_CLIENT],
            "order" => "DATE_VENTE DESC",
            "limit" => 5
        ]);

        foreach ($ventes as $vente) {
            $totalSale = 0;
            $concernerItems = Concerner::find([
                "conditions" => "ID_VENTE = :id_vente:",
                "bind" => ["id_vente" => $vente->ID_VENTE]
            ]);
            foreach ($concernerItems as $item) {
                $totalSale += ($item->QUANTITE_VENDUE * $item->PRIX_UNITAIRE_VENDUE);
            }
            $purchases[] = [
                'id' => $vente->ID_VENTE,
                'date' => $vente->DATE_VENTE,
                'total' => $totalSale,
                'reference' => 'SALE-' . str_pad($vente->ID_VENTE, 3, '0', STR_PAD_LEFT)
            ];
        }

        $this->view->setVars([
            'client' => $client,
            'totalSpent' => $totalSpent,
            'purchaseCount' => $purchaseCount,
            'purchases' => $purchases,
            'canModifyClients' => true,
            'canDeleteClients' => true
        ]);
        $this->view->pick("clients/view");
    }

    public function addAction()
    {
        $this->view->disable();

        if ($this->request->isPost()) {
            // Récupérer les données JSON ou POST
            $rawBody = $this->request->getRawBody();
            $data = $rawBody ? json_decode($rawBody, true) : $this->request->getPost();

            $nom = $data['nom_client'] ?? null;
            $prenom = $data['prenom_client'] ?? null;
            $telephone = $data['telephone_client'] ?? null;
            $adresse = $data['adresse_client'] ?? null;

            error_log('Données reçues pour ajout client: ' . print_r($data, true));

            if (empty($nom) || empty($prenom) || empty($telephone)) {
                return $this->response->setJsonContent([
                    'status' => 'error',
                    'message' => 'Veuillez remplir tous les champs obligatoires (Nom, Prénom, Téléphone).'
                ]);
            }

            $client = new Client();
            $client->NOM_CLIENT = $nom;
            $client->PRENOM_CLIENT = $prenom;
            $client->TELEPHONE_CLIENT = $telephone;
            $client->ADRESSE_CLIENT = $adresse ?: null;

            if ($client->save()) {
                error_log('Client ajouté: ID=' . $client->ID_CLIENT);
                return $this->response->setJsonContent([
                    'status' => 'success',
                    'message' => 'Client ajouté avec succès.',
                    'client' => $client->toArray()
                ]);
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $client->getMessages());
                error_log('Erreur sauvegarde client: ' . implode(', ', $messages));
                return $this->response->setJsonContent([
                    'status' => 'error',
                    'message' => 'Erreur lors de l\'ajout du client : ' . implode(', ', $messages)
                ]);
            }
        }

        return $this->response->setJsonContent([
            'status' => 'error',
            'message' => 'Méthode non autorisée.'
        ])->setStatusCode(405, 'Method Not Allowed');
    }

    public function editAction($id)
    {
        $client = Client::findFirst($id);
        if (!$client) {
            $this->flash->error("Client non trouvé.");
            return $this->response->redirect('/clients');
        }

        if ($this->request->isPost()) {
            $client->NOM_CLIENT = $this->request->getPost('nom_client', 'string');
            $client->PRENOM_CLIENT = $this->request->getPost('prenom_client', 'string');
            $client->TELEPHONE_CLIENT = $this->request->getPost('telephone_client', 'string');
            $client->ADRESSE_CLIENT = $this->request->getPost('adresse_client', 'string') ?: null;

            if ($client->save()) {
                $this->flash->success("Client modifié avec succès.");
                return $this->response->redirect('/clients');
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $client->getMessages());
                $this->flash->error("Erreur lors de la modification : " . implode(', ', $messages));
            }
        }

        $this->view->setVars([
            'client' => $client,
            'canModifyClients' => true
        ]);
        $this->view->pick("clients/edit");
    }

    public function deleteAction($id)
    {
        $client = Client::findFirst($id);
        if (!$client) {
            $this->flash->error("Client non trouvé.");
            return $this->response->redirect('/clients');
        }

        $ventes = Vente::count([
            "conditions" => "ID_CLIENT = :id_client:",
            "bind" => ["id_client" => $id]
        ]);

        if ($ventes > 0) {
            $this->flash->error("Impossible de supprimer ce client car il a des ventes associées.");
            return $this->response->redirect('/clients');
        }

        if ($client->delete()) {
            $this->flash->success("Client supprimé avec succès.");
        } else {
            $messages = array_map(function ($msg) {
                return $msg->getMessage();
            }, $client->getMessages());
            $this->flash->error("Erreur lors de la suppression : " . implode(', ', $messages));
        }

        return $this->response->redirect('/clients');
    }
}
