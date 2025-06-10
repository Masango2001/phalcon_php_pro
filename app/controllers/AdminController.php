use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class AdminController extends Controller
{
    // Vérifie si l'utilisateur est admin
    private function isAdmin()
    {
        return $this->session->has('auth') && $this->session->get('auth')['role'] === 'admin';
    }

    // Tableau de bord centralisé pour l'admin
    public function dashboardAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }

        // Statistiques globales
        $nbProduits = Produit::count();
        $nbCategories = Categorie::count();
        $nbClients = Client::count();
        $nbStocks = Stock::count();
        $nbApprovisionnements = Approvisionnement::count();
        $nbVentes = Vente::count();
        $nbUtilisateurs = Utilisateur::count();

        $derniersProduits = Produit::find([
            'order' => 'id_produit DESC',
            'limit' => 5
        ]);
        $derniersClients = Client::find([
            'order' => 'id_client DESC',
            'limit' => 5
        ]);
        $derniersVentes = Vente::find([
            'order' => 'date_vente DESC',
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
        $this->view->nbVentes = $nbVentes;
        $this->view->nbUtilisateurs = $nbUtilisateurs;
        $this->view->derniersProduits = $derniersProduits;
        $this->view->derniersClients = $derniersClients;
        $this->view->derniersVentes = $derniersVentes;
        $this->view->stocksFaibles = $stocksFaibles;

        // Liens de navigation rapide pour l'admin
        $this->view->adminLinks = [
            ['label' => 'Utilisateurs', 'url' => 'admin/utilisateurs'],
            ['label' => 'Produits', 'url' => 'admin/produits'],
            ['label' => 'Catégories', 'url' => 'admin/categories'],
            ['label' => 'Clients', 'url' => 'admin/clients'],
            ['label' => 'Stocks', 'url' => 'admin/stocks'],
            ['label' => 'Approvisionnements', 'url' => 'admin/approvisionnements'],
            ['label' => 'Ventes', 'url' => 'admin/ventes'],
        ];
    }

    // Gestion des utilisateurs (CRUD)
    public function utilisateursAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $utilisateurs = Utilisateur::find();
        $this->view->utilisateurs = $utilisateurs;
    }

    public function createUtilisateurAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        if ($this->request->isPost()) {
            $utilisateur = new Utilisateur();
            $utilisateur->assign(
                $this->request->getPost(),
                ['username', 'password', 'email', 'role']
            );
            if ($utilisateur->save()) {
                $this->flashSession->success("Utilisateur ajouté !");
                return $this->response->redirect('admin/utilisateurs');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout.");
            }
        }
    }

    public function editUtilisateurAction($id)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $utilisateur = Utilisateur::findFirstById_utilisateur($id);
        if (!$utilisateur) {
            $this->flashSession->error("Utilisateur non trouvé.");
            return $this->response->redirect('admin/utilisateurs');
        }
        if ($this->request->isPost()) {
            $utilisateur->assign(
                $this->request->getPost(),
                ['username', 'email', 'role']
            );
            $password = $this->request->getPost('password', 'string');
            if (!empty($password)) {
                $utilisateur->password = $password;
            }
            if ($utilisateur->save()) {
                $this->flashSession->success("Utilisateur modifié !");
                return $this->response->redirect('admin/utilisateurs');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        $this->view->utilisateur = $utilisateur;
    }

    public function deleteUtilisateurAction($id)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $utilisateur = Utilisateur::findFirstById_utilisateur($id);
        if ($utilisateur && $utilisateur->delete()) {
            $this->flashSession->success("Utilisateur supprimé !");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('admin/utilisateurs');
    }

    // Gestion centralisée des produits
    public function produitsAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $produits = Produit::find();
        $this->view->produits = $produits;
    }

    // Gestion centralisée des catégories
    public function categoriesAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $categories = Categorie::find();
        $this->view->categories = $categories;
    }

    // Gestion centralisée des clients
    public function clientsAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $clients = Client::find();
        $this->view->clients = $clients;
    }

    // Gestion centralisée des stocks
    public function stocksAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $stocks = Stock::find();
        $this->view->stocks = $stocks;
    }

    // Gestion centralisée des approvisionnements
    public function approvisionnementsAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $approvisionnements = Approvisionnement::find([
            'order' => 'date_approvisionnement DESC'
        ]);
        $this->view->approvisionnements = $approvisionnements;
    }

    // Gestion centralisée des ventes
    public function ventesAction()
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $ventes = Vente::find([
            'order' => 'date_vente DESC'
        ]);
        $this->view->ventes = $ventes;
    }

    // Gestion centralisée des produits concernés par une vente
    public function concernerAction($id_vente)
    {
        if (!$this->isAdmin()) {
            $this->flashSession->error("Accès refusé.");
            return $this->response->redirect('auth/login');
        }
        $concerner = Concerner::find([
            'conditions' => 'id_vente = ?1',
            'bind' => [1 => $id_vente]
        ]);
        $this->view->concerner = $concerner;
        $this->view->id_vente = $id_vente;
    }
}