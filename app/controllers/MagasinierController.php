use Phalcon\Mvc\Controller;

<?php


class MagasinierController extends Controller
{
    // Tableau de bord du magasinier
    public function indexAction()
    {
        $nbProduits = Produit::count();
        $nbCategories = Categorie::count();
        $nbClients = Client::count();
        $nbStocks = Stock::count();
        $nbApprovisionnements = Approvisionnement::count();

        $derniersProduits = Produit::find([
            'order' => 'id_produit DESC',
            'limit' => 5
        ]);

        $derniersClients = Client::find([
            'order' => 'id_client DESC',
            'limit' => 5
        ]);

        $derniersApprovisionnements = Approvisionnement::find([
            'order' => 'date_approvisionnement DESC',
            'limit' => 5
        ]);

        $stocksFaibles = Stock::find([
            'conditions' => 'quantite_stock < 10',
            'limit' => 5
        ]);

        $this->view->nbProduits = $nbProduits;
        $this->view->nbCategories = $nbCategories;
        $this->view->nbClients = $nbClients;
        $this->view->nbStocks = $nbStocks;
        $this->view->nbApprovisionnements = $nbApprovisionnements;
        $this->view->derniersProduits = $derniersProduits;
        $this->view->derniersClients = $derniersClients;
        $this->view->derniersApprovisionnements = $derniersApprovisionnements;
        $this->view->stocksFaibles = $stocksFaibles;
    }

    // Liste des catégories
    public function categoriesAction()
    {
        $categories = Categorie::find();
        $this->view->categories = $categories;
    }

    // CRUD Catégorie
    public function createCategorieAction()
    {
        if ($this->request->isPost()) {
            $categorie = new Categorie();
            $categorie->assign(
                $this->request->getPost(),
                ['nom_categorie']
            );
            if ($categorie->save()) {
                $this->flashSession->success("Catégorie ajoutée !");
                return $this->response->redirect('magasinier/categories');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout.");
            }
        }
    }

    public function editCategorieAction($id)
    {
        $categorie = Categorie::findFirstByIdCategorie($id);
        if (!$categorie) {
            $this->flashSession->error("Catégorie non trouvée.");
            return $this->response->redirect('magasinier/categories');
        }
        if ($this->request->isPost()) {
            $categorie->assign(
                $this->request->getPost(),
                ['nom_categorie']
            );
            if ($categorie->save()) {
                $this->flashSession->success("Catégorie modifiée !");
                return $this->response->redirect('magasinier/categories');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        $this->view->categorie = $categorie;
    }

    public function deleteCategorieAction($id)
    {
        $categorie = Categorie::findFirstByIdCategorie($id);
        if ($categorie && $categorie->delete()) {
            $this->flashSession->success("Catégorie supprimée !");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('magasinier/categories');
    }

    // Liste des clients
    public function clientsAction()
    {
        $clients = Client::find();
        $this->view->clients = $clients;
    }

    // CRUD Client
    public function createClientAction()
    {
        if ($this->request->isPost()) {
            $client = new Client();
            $client->assign(
                $this->request->getPost(),
                ['nom_client', 'email', 'telephone']
            );
            if ($client->save()) {
                $this->flashSession->success("Client ajouté !");
                return $this->response->redirect('magasinier/clients');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout.");
            }
        }
    }

    public function editClientAction($id)
    {
        $client = Client::findFirstByIdClient($id);
        if (!$client) {
            $this->flashSession->error("Client non trouvé.");
            return $this->response->redirect('magasinier/clients');
        }
        if ($this->request->isPost()) {
            $client->assign(
                $this->request->getPost(),
                ['nom_client', 'email', 'telephone']
            );
            if ($client->save()) {
                $this->flashSession->success("Client modifié !");
                return $this->response->redirect('magasinier/clients');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        $this->view->client = $client;
    }

    public function deleteClientAction($id)
    {
        $client = Client::findFirstByIdClient($id);
        if ($client && $client->delete()) {
            $this->flashSession->success("Client supprimé !");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('magasinier/clients');
    }

    // Liste des stocks
    public function stocksAction()
    {
        $stocks = Stock::find();
        $this->view->stocks = $stocks;
    }

    // CRUD Stock
    public function createStockAction()
    {
        if ($this->request->isPost()) {
            $stock = new Stock();
            $stock->assign(
                $this->request->getPost(),
                ['id_produit', 'quantite_stock']
            );
            if ($stock->save()) {
                $this->flashSession->success("Stock ajouté !");
                return $this->response->redirect('magasinier/stocks');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout.");
            }
        }
    }

    public function editStockAction($id)
    {
        $stock = Stock::findFirstByIdStock($id);
        if (!$stock) {
            $this->flashSession->error("Stock non trouvé.");
            return $this->response->redirect('magasinier/stocks');
        }
        if ($this->request->isPost()) {
            $stock->assign(
                $this->request->getPost(),
                ['id_produit', 'quantite_stock']
            );
            if ($stock->save()) {
                $this->flashSession->success("Stock modifié !");
                return $this->response->redirect('magasinier/stocks');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        $this->view->stock = $stock;
    }

    public function deleteStockAction($id)
    {
        $stock = Stock::findFirstByIdStock($id);
        if ($stock && $stock->delete()) {
            $this->flashSession->success("Stock supprimé !");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('magasinier/stocks');
    }

    // Liste des approvisionnements
    public function approvisionnementsAction()
    {
        $approvisionnements = Approvisionnement::find([
            'order' => 'date_approvisionnement DESC'
        ]);
        $this->view->approvisionnements = $approvisionnements;
    }

    // CRUD Approvisionnement
    public function createApprovisionnementAction()
    {
        if ($this->request->isPost()) {
            $appro = new Approvisionnement();
            $appro->assign(
                $this->request->getPost(),
                ['id_produit', 'quantite', 'date_approvisionnement']
            );
            if ($appro->save()) {
                $this->flashSession->success("Approvisionnement ajouté !");
                return $this->response->redirect('magasinier/approvisionnements');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout.");
            }
        }
    }

    public function editApprovisionnementAction($id)
    {
        $appro = Approvisionnement::findFirstByIdApprovisionnement($id);
        if (!$appro) {
            $this->flashSession->error("Approvisionnement non trouvé.");
            return $this->response->redirect('magasinier/approvisionnements');
        }
        if ($this->request->isPost()) {
            $appro->assign(
                $this->request->getPost(),
                ['id_produit', 'quantite', 'date_approvisionnement']
            );
            if ($appro->save()) {
                $this->flashSession->success("Approvisionnement modifié !");
                return $this->response->redirect('magasinier/approvisionnements');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        $this->view->approvisionnement = $appro;
    }

    public function deleteApprovisionnementAction($id)
    {
        $appro = Approvisionnement::findFirstByIdApprovisionnement($id);
        if ($appro && $appro->delete()) {
            $this->flashSession->success("Approvisionnement supprimé !");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('magasinier/approvisionnements');
    }
}    