<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Fournisseur;
use GestionStockVente\Models\Approvisionnement;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;

class SuppliersController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Fournisseurs');
    }

    public function indexAction()
    {
        $searchQuery = $this->request->getQuery('search', 'string', '');
        $sortBy = $this->request->getQuery('sort_by', 'string', 'recents');

        $suppliersData = $this->getSuppliersData($searchQuery, $sortBy);

        $this->view->setVars([
            'suppliers' => $suppliersData,
            'searchQuery' => $searchQuery,
            'sortBy' => $sortBy,
            'canViewSuppliersList' => true,
            'canModifySuppliers' => true,
            'canDeleteSuppliers' => true
        ]);
        $this->view->pick("suppliers/index");
    }

    public function getSuppliersContentAction(): Response
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            $pageTitle = 'Gestion des Fournisseurs';
            $searchQuery = $this->request->getQuery('search', 'string', '');
            $sortBy = $this->request->getQuery('sort_by', 'string', 'recents');

            $suppliersData = $this->getSuppliersData($searchQuery, $sortBy);

            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
            $this->view->setVars([
                'suppliers' => $suppliersData,
                'searchQuery' => $searchQuery,
                'sortBy' => $sortBy,
                'canViewSuppliersList' => true,
                'canModifySuppliers' => true,
                'canDeleteSuppliers' => true
            ]);

            $renderedHtml = $this->view->getRender('suppliers', 'index');

            $response->setJsonContent([
                'success' => true,
                'title' => $pageTitle,
                'html' => $renderedHtml
            ]);
        } catch (\Throwable $e) {
            error_log('Erreur AJAX Suppliers: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $response->setJsonContent([
                'success' => false,
                'message' => 'Erreur lors du chargement du contenu des fournisseurs: ' . $e->getMessage()
            ]);
            $response->setStatusCode(500, 'Internal Server Error');
        }

        return $response;
    }

    private function getSuppliersData(string $searchQuery, string $sortBy): array
    {
        $conditions = [];
        $bind = [];
        $order = 'ID_FOURNISSEUR DESC';

        if (!empty($searchQuery)) {
            $conditions[] = "(NOM_COMPLET_FOURNISSEUR LIKE :search_query: OR ADRESSE_FOURNISSEUR LIKE :search_query: OR EMAIL_FOURNISSEUR LIKE :search_query: OR TELEPHONE_FOURNISSEUR LIKE :search_query:)";
            $bind['search_query'] = '%' . $searchQuery . '%';
        }

        switch ($sortBy) {
            case 'name':
                $order = 'NOM_COMPLET_FOURNISSEUR ASC';
                break;
            case 'city':
                $order = 'ADRESSE_FOURNISSEUR ASC';
                break;
            case 'email':
                $order = 'EMAIL_FOURNISSEUR ASC';
                break;
            case 'phone':
                $order = 'TELEPHONE_FOURNISSEUR ASC';
                break;
            case 'recents':
            default:
                $order = 'ID_FOURNISSEUR DESC';
                break;
        }

        $findParams = [
            'order' => $order
        ];

        if (!empty($conditions)) {
            $findParams['conditions'] = implode(' AND ', $conditions);
            $findParams['bind'] = $bind;
        }

        $suppliers = Fournisseur::find($findParams);

        $suppliersData = [];
        foreach ($suppliers as $fournisseur) {
            $suppliersData[] = [
                'id' => $fournisseur->ID_FOURNISSEUR,
                'name' => $fournisseur->NOM_COMPLET_FOURNISSEUR,
                'adresse' => $fournisseur->ADRESSE_FOURNISSEUR ?: 'N/A',
                'email' => $fournisseur->EMAIL_FOURNISSEUR ?: 'N/A',
                'phone' => $fournisseur->TELEPHONE_FOURNISSEUR ?: 'N/A'
            ];
        }

        error_log('Fournisseurs récupérés: ' . count($suppliersData));

        return $suppliersData;
    }

    public function addAction(): Response
    {
        $this->view->disable();
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        try {
            if (!$this->request->isPost()) {
                return $response->setJsonContent([
                    'status' => 'error',
                    'message' => 'Méthode non autorisée.'
                ])->setStatusCode(405, 'Method Not Allowed');
            }

            // Récupérer les données JSON ou POST
            $rawBody = $this->request->getRawBody();
            $data = $rawBody ? json_decode($rawBody, true) : $this->request->getPost();

            error_log('Données reçues pour ajout fournisseur: ' . print_r($data, true));

            $nomComplet = $data['nom_complet_fournisseur'] ?? null;
            $adresse = $data['adresse_fournisseur'] ?? null;
            $email = $data['email_fournisseur'] ?? null;
            $telephone = $data['telephone_fournisseur'] ?? null;

            if (empty($nomComplet) || empty($telephone)) {
                return $response->setJsonContent([
                    'status' => 'error',
                    'message' => 'Veuillez remplir les champs obligatoires (Nom complet, Téléphone).'
                ])->setStatusCode(400, 'Bad Request');
            }

            $fournisseur = new Fournisseur();
            $fournisseur->NOM_COMPLET_FOURNISSEUR = $nomComplet;
            $fournisseur->ADRESSE_FOURNISSEUR = $adresse ?: null;
            $fournisseur->EMAIL_FOURNISSEUR = $email ?: null;
            $fournisseur->TELEPHONE_FOURNISSEUR = $telephone;

            if ($fournisseur->save()) {
                error_log('Fournisseur ajouté: ID=' . $fournisseur->ID_FOURNISSEUR);
                return $response->setJsonContent([
                    'status' => 'success',
                    'message' => 'Fournisseur ajouté avec succès!',
                    'fournisseur' => $fournisseur->toArray()
                ]);
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $fournisseur->getMessages());
                error_log('Erreur sauvegarde fournisseur: ' . implode(', ', $messages));
                return $response->setJsonContent([
                    'status' => 'error',
                    'message' => 'Erreur lors de l\'ajout du fournisseur: ' . implode(', ', $messages)
                ])->setStatusCode(400, 'Bad Request');
            }
        } catch (\Throwable $e) {
            error_log('Exception dans addAction: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $response->setJsonContent([
                'status' => 'error',
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ])->setStatusCode(500, 'Internal Server Error');
        }
    }

    public function viewAction($id)
    {
        $fournisseur = Fournisseur::findFirst($id);
        if (!$fournisseur) {
            $this->flash->error("Fournisseur non trouvé.");
            return $this->response->redirect('/suppliers');
        }

        $this->view->setVars([
            'fournisseur' => $fournisseur,
            'canModifySuppliers' => true,
            'canDeleteSuppliers' => true
        ]);
        $this->view->pick("suppliers/view");
    }

    public function editAction($id)
    {
        $fournisseur = Fournisseur::findFirst($id);
        if (!$fournisseur) {
            $this->flash->error("Fournisseur non trouvé.");
            return $this->response->redirect('/suppliers');
        }

        if ($this->request->isPost()) {
            $fournisseur->NOM_COMPLET_FOURNISSEUR = $this->request->getPost('nom_complet_fournisseur', 'string');
            $fournisseur->ADRESSE_FOURNISSEUR = $this->request->getPost('adresse_fournisseur', 'string') ?: null;
            $fournisseur->EMAIL_FOURNISSEUR = $this->request->getPost('email_fournisseur', 'string') ?: null;
            $fournisseur->TELEPHONE_FOURNISSEUR = $this->request->getPost('telephone_fournisseur', 'string');

            if ($fournisseur->save()) {
                $this->flash->success("Fournisseur modifié avec succès.");
                return $this->response->redirect('/suppliers');
            } else {
                $messages = array_map(function ($msg) {
                    return $msg->getMessage();
                }, $fournisseur->getMessages());
                $this->flash->error("Erreur lors de la modification : " . implode(', ', $messages));
            }
        }

        $this->view->setVars([
            'fournisseur' => $fournisseur,
            'canModifySuppliers' => true
        ]);
        $this->view->pick("suppliers/edit");
    }

    public function deleteAction($id)
    {
        $fournisseur = Fournisseur::findFirst($id);
        if (!$fournisseur) {
            $this->flash->error("Fournisseur non trouvé.");
            return $this->response->redirect('/suppliers');
        }

        $approvisionnements = Approvisionnement::count([
            "conditions" => "ID_FOURNISSEUR = :id_fournisseur:",
            "bind" => ["id_fournisseur" => $id]
        ]);

        if ($approvisionnements > 0) {
            $this->flash->error("Impossible de supprimer ce fournisseur car il a des approvisionnements associés.");
            return $this->response->redirect('/suppliers');
        }

        if ($fournisseur->delete()) {
            $this->flash->success("Fournisseur supprimé avec succès.");
        } else {
            $messages = array_map(function ($msg) {
                return $msg->getMessage();
            }, $fournisseur->getMessages());
            $this->flash->error("Erreur lors de la suppression : " . implode(', ', $messages));
        }

        return $this->response->redirect('/suppliers');
    }
}
