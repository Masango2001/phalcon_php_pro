use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class CategorieController extends Controller
{
    // List all categories
    public function indexAction()
    {
        $categories = Categorie::find();
        $this->view->categories = $categories;
    }

    // Show a single category
    public function showAction($id)
    {
        $categorie = Categorie::findFirstById_categorie($id);
        if (!$categorie) {
            $this->flashSession->error("Catégorie non trouvée.");
            return $this->response->redirect('categorie/index');
        }
        $this->view->categorie = $categorie;
    }

    // Create a new category
    public function createAction()
    {
        if ($this->request->isPost()) {
            $categorie = new Categorie();
            $categorie->nom_categorie = $this->request->getPost('nom_categorie', 'string');

            if ($categorie->save()) {
                $this->flashSession->success("Catégorie ajoutée avec succès.");
                return $this->response->redirect('categorie/index');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout de la catégorie.");
            }
        }
    }

    // Edit a category
    public function editAction($id)
    {
        $categorie = Categorie::findFirstById_categorie($id);
        if (!$categorie) {
            $this->flashSession->error("Catégorie non trouvée.");
            return $this->response->redirect('categorie/index');
        }

        if ($this->request->isPost()) {
            $categorie->nom_categorie = $this->request->getPost('nom_categorie', 'string');
            if ($categorie->save()) {
                $this->flashSession->success("Catégorie modifiée avec succès.");
                return $this->response->redirect('categorie/index');
            } else {
                $this->flashSession->error("Erreur lors de la modification de la catégorie.");
            }
        }

        $this->view->categorie = $categorie;
    }

    // Delete a category
    public function deleteAction($id)
    {
        $categorie = Categorie::findFirstById_categorie($id);
        if ($categorie) {
            if ($categorie->delete()) {
                $this->flashSession->success("Catégorie supprimée avec succès.");
            } else {
                $this->flashSession->error("Erreur lors de la suppression de la catégorie.");
            }
        } else {
            $this->flashSession->error("Catégorie non trouvée.");
        }
        return $this->response->redirect('categorie/index');
    }
}