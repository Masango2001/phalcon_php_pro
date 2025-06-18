<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Stock;
use GestionStockVente\Models\Produit;
use Phalcon\Mvc\Model\ResultsetInterface;

class StocksController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Stocks');
    }

    public function indexAction()
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';

        $canAddStocks = false;
        $canViewStocksList = false;
        $canModifyStocks = false;
        $canDeleteStocks = false;

        switch ($role) {
            case 'admin':
                $canAddStocks = true;
                $canViewStocksList = true;
                $canModifyStocks = true;
                $canDeleteStocks = true;
                break;
            case 'magasinier':
                $canAddStocks = true;
                $canViewStocksList = true;
                $canModifyStocks = true;
                $canDeleteStocks = true;
                break;
            case 'vendeur':

                $canViewStocksList = true;
                $canAddStocks = false;
                $canModifyStocks = false;
                $canDeleteStocks = false;
                break;
            default:
                $canViewStocksList = false;
                break;
        }

        $stocksData = [];
        try {
            if ($canViewStocksList) {

                $stocksResultSet = $this->modelsManager->createQuery(
                    "SELECT s.ID_STOCK, s.QUANTITE_STOCK, s.DATE_MISEJOUR, p.NOM_PRODUIT, p.ID_PRODUIT
                     FROM GestionStockVente\\Models\\Stock s
                     JOIN GestionStockVente\\Models\\Produit p ON s.ID_PRODUIT = p.ID_PRODUIT
                     ORDER BY s.DATE_MISEJOUR DESC"
                )->execute();

                if ($stocksResultSet instanceof ResultsetInterface && $stocksResultSet->count() > 0) {
                    foreach ($stocksResultSet as $row) {
                        $stocksData[] = [
                            'ID_STOCK'         => $row->ID_STOCK,
                            'ID_PRODUIT'       => $row->ID_PRODUIT,
                            'NOM_PRODUIT'      => $row->NOM_PRODUIT,
                            'QUANTITE_STOCK'   => $row->QUANTITE_STOCK,
                            'DATE_MISEJOUR'    => (new \DateTime($row->DATE_MISEJOUR))->format('d/m/Y H:i:s'), // Formater la date
                            'STATUT_STOCK'     => $this->getStockStatus($row->QUANTITE_STOCK), // Fonction utilitaire pour le statut
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Erreur dans StocksController::indexAction : " . $e->getMessage() . " sur la ligne " . $e->getLine() . " dans " . $e->getFile());
            $this->flash->error("Une erreur s'est produite lors de la récupération des stocks. Veuillez réessayer. " . $e->getMessage());
            $stocksData = [];
        }

        $this->view->setVars([
            'stocks'            => $stocksData,
            'canAddStocks'      => $canAddStocks,
            'canViewStocksList' => $canViewStocksList,
            'canModifyStocks'   => $canModifyStocks,
            'canDeleteStocks'   => $canDeleteStocks,
            'currentUserRole'   => $role
        ]);

        $this->view->pick('stocks/index');
    }

    // Helper function pour déterminer le statut du stock
    private function getStockStatus(int $quantity): string
    {
        if ($quantity <= 5 && $quantity > 0) {
            return 'Faible Stock';
        } elseif ($quantity === 0) {
            return 'Rupture de Stock';
        }
        return 'Disponible';
    }

    public function createAction()
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission d'ajouter une entrée de stock.");
            return $this->response->redirect('stocks');
        }

        if ($this->request->isPost()) {
            $stock = new Stock();

            $idProduit = $this->request->getPost('id_produit', 'int');
            $quantiteStock = $this->request->getPost('quantite_stock', 'int');

            if (empty($idProduit) || $quantiteStock < 0) {
                $this->flash->error("Veuillez sélectionner un produit et entrer une quantité valide.");
                return $this->response->redirect('stocks/create');
            }

            // Vérifier si un stock existe déjà pour ce produit
            $existingStock = Stock::findFirst([
                'conditions' => 'ID_PRODUIT = :id_produit:',
                'bind' => ['id_produit' => $idProduit]
            ]);

            if ($existingStock) {
                // Si un stock existe, mettez à jour la quantité
                $existingStock->QUANTITE_STOCK += $quantiteStock; // Ajoute à la quantité existante
                $existingStock->DATE_MISEJOUR = date('Y-m-d H:i:s');

                if (!$existingStock->save()) {
                    foreach ($existingStock->getMessages() as $message) {
                        $this->flash->error("Erreur lors de la mise à jour du stock existant : " . (string) $message);
                    }
                    return $this->response->redirect('stocks/create');
                }
                $this->flash->success("Le stock pour le produit a été mis à jour avec succès (nouvelle quantité: " . $existingStock->QUANTITE_STOCK . ").");
            } else {
                // Sinon, créez une nouvelle entrée de stock
                $stock->ID_PRODUIT = $idProduit;
                $stock->QUANTITE_STOCK = $quantiteStock;
                $stock->DATE_MISEJOUR = date('Y-m-d H:i:s');

                if (!$stock->save()) {
                    foreach ($stock->getMessages() as $message) {
                        $this->flash->error("Erreur lors de la création de l'entrée de stock : " . (string) $message);
                    }
                    return $this->response->redirect('stocks/create');
                }
                $this->flash->success("Une nouvelle entrée de stock a été ajoutée avec succès.");
            }
            return $this->response->redirect('stocks');
        }

        // Pour l'affichage du formulaire : récupérer tous les produits disponibles
        $products = Produit::find();
        $this->view->setVar('products', $products);
        $this->view->pick('stocks/create');
    }

    public function editAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission de modifier une entrée de stock.");
            return $this->response->redirect('stocks');
        }

        $stock = Stock::findFirstByID_STOCK($id);
        if (!$stock) {
            $this->flash->error("L'entrée de stock n'existe pas.");
            return $this->response->redirect('stocks');
        }

        if ($this->request->isPost()) {
            $quantiteStock = $this->request->getPost('quantite_stock', 'int');
            // La date de mise à jour peut être automatique ou modifiable
            $dateMiseJour = $this->request->getPost('date_misejour', 'string');

            if ($quantiteStock < 0) {
                $this->flash->error("Veuillez entrer une quantité valide.");
                return $this->response->redirect('stocks/edit/' . $id);
            }

            $stock->QUANTITE_STOCK = $quantiteStock;
            // Si vous voulez permettre la modification de la date, sinon laissez-la automatique
            $stock->DATE_MISEJOUR = !empty($dateMiseJour) ? $dateMiseJour : date('Y-m-d H:i:s');

            if (!$stock->save()) {
                foreach ($stock->getMessages() as $message) {
                    $this->flash->error("Erreur lors de la mise à jour de l'entrée de stock : " . (string) $message);
                }
                return $this->response->redirect('stocks/edit/' . $id);
            }

            $this->flash->success("L'entrée de stock a été mise à jour avec succès.");
            return $this->response->redirect('stocks');
        }

        // Pour l'affichage du formulaire d'édition
        $this->view->setVar('stock', $stock);
        // On peut aussi passer le produit associé pour afficher son nom
        $this->view->setVar('product', $stock->getProduit()); // Utilise l'alias 'Produit' défini dans le modèle Stock
        $this->view->pick('stocks/edit');
    }

    public function deleteAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission de supprimer une entrée de stock.");
            return $this->response->redirect('stocks');
        }

        $stock = Stock::findFirstByID_STOCK($id);
        if (!$stock) {
            $this->flash->error("L'entrée de stock n'existe pas.");
            return $this->response->redirect('stocks');
        }

        if ($stock->delete() === false) {
            foreach ($stock->getMessages() as $message) {
                $this->flash->error("Erreur lors de la suppression de l'entrée de stock : " . (string) $message);
            }
        } else {
            $this->flash->success("L'entrée de stock a été supprimée avec succès.");
        }
        return $this->response->redirect('stocks');
    }

    public function viewAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role === 'guest') { // Ou si la permission spécifique n'est pas accordée
            $this->flash->error("Vous n'avez pas la permission de voir les détails d'un stock.");
            return $this->response->redirect('stocks');
        }

        $stock = Stock::findFirstByID_STOCK($id);
        if (!$stock) {
            $this->flash->error("L'entrée de stock n'existe pas.");
            return $this->response->redirect('stocks');
        }

        // Charger le produit associé pour afficher son nom
        $product = $stock->getProduit(); // Utilise l'alias 'Produit' défini dans le modèle Stock

        $stockDetails = [
            'ID_STOCK'         => $stock->ID_STOCK,
            'NOM_PRODUIT'      => $product ? htmlspecialchars($product->NOM_PRODUIT) : 'Produit inconnu',
            'QUANTITE_STOCK'   => $stock->QUANTITE_STOCK,
            'DATE_MISEJOUR'    => (new \DateTime($stock->DATE_MISEJOUR))->format('d/m/Y H:i:s'),
            'STATUT_STOCK'     => $this->getStockStatus($stock->QUANTITE_STOCK),
        ];

        $this->view->setVar('stockDetails', $stockDetails);
        $this->view->setVar('canModifyStocks', ($role === 'admin' || $role === 'magasinier'));
        $this->view->pick('stocks/view');
    }
}
