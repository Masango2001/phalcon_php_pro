use Phalcon\Mvc\Controller;

<?php


class ProduitController extends Controller
{
    // Afficher la liste des produits
    public function indexAction()
    {
        $produits = Produit::find();
        $this->view->produits = $produits;
    }

    // Afficher le formulaire d'ajout de produit
    public function newAction()
    {
    }

    // Ajouter un produit (traitement du formulaire)
    public function createAction()
    {
        if ($this->request->isPost()) {
            $produit = new Produit();
            $produit->id_categorie = $this->request->getPost('id_categorie', 'int');
            $produit->nom_produit = $this->request->getPost('nom_produit', 'string');

            if ($produit->save()) {
                $this->flashSession->success("Produit ajouté avec succès.");
                return $this->response->redirect('produit/index');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout du produit.");
            }
        }
    }

    // Afficher le formulaire de modification
    public function editAction($id_produit)
    {
        $produit = Produit::findFirstById_produit($id_produit);
        if (!$produit) {
            $this->flashSession->error("Produit non trouvé.");
            return $this->response->redirect('produit/index');
        }
        $this->view->produit = $produit;
    }

    // Modifier un produit
    public function updateAction($id_produit)
    {
        if ($this->request->isPost()) {
            $produit = Produit::findFirstById_produit($id_produit);
            if (!$produit) {
                $this->flashSession->error("Produit non trouvé.");
                return $this->response->redirect('produit/index');
            }
            $produit->id_categorie = $this->request->getPost('id_categorie', 'int');
            $produit->nom_produit = $this->request->getPost('nom_produit', 'string');

            if ($produit->save()) {
                $this->flashSession->success("Produit modifié avec succès.");
                return $this->response->redirect('produit/index');
            } else {
                $this->flashSession->error("Erreur lors de la modification du produit.");
            }
        }
    }

    // Supprimer un produit
    public function deleteAction($id_produit)
    {
        $produit = Produit::findFirstById_produit($id_produit);
        if ($produit && $produit->delete()) {
            $this->flashSession->success("Produit supprimé avec succès.");
        } else {
            $this->flashSession->error("Erreur lors de la suppression du produit.");
        }
        return $this->response->redirect('produit/index');
    }
}

