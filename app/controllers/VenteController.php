use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class VenteController extends Controller
{
    // Afficher toutes les ventes
    public function indexAction()
    {
        $ventes = Vente::find();
        $this->view->ventes = $ventes;
    }

    // Afficher une vente spécifique
    public function showAction($id)
    {
        $vente = Vente::findFirstById_vente($id);
        if (!$vente) {
            $this->flashSession->error("Vente non trouvée");
            return $this->response->redirect('vente/index');
        }
        $this->view->vente = $vente;
    }

    // Ajouter une nouvelle vente
    public function createAction()
    {
        if ($this->request->isPost()) {
            $vente = new Vente();
            $vente->id_client = $this->request->getPost('id_client', 'int');
            $vente->id_utilisateur = $this->request->getPost('id_utilisateur', 'int');
            $vente->date_vente = $this->request->getPost('date_vente');
            $vente->id_produit = $this->request->getPost('id_produit', 'int');

            if ($vente->save()) {
                $this->flashSession->success("Vente ajoutée avec succès");
                return $this->response->redirect('vente/index');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout de la vente");
            }
        }
    }

    // Modifier une vente existante
    public function editAction($id)
    {
        $vente = Vente::findFirstById_vente($id);
        if (!$vente) {
            $this->flashSession->error("Vente non trouvée");
            return $this->response->redirect('vente/index');
        }

        if ($this->request->isPost()) {
            $vente->id_client = $this->request->getPost('id_client', 'int');
            $vente->id_utilisateur = $this->request->getPost('id_utilisateur', 'int');
            $vente->date_vente = $this->request->getPost('date_vente');
            $vente->id_produit = $this->request->getPost('id_produit', 'int');

            if ($vente->save()) {
                $this->flashSession->success("Vente modifiée avec succès");
                return $this->response->redirect('vente/index');
            } else {
                $this->flashSession->error("Erreur lors de la modification de la vente");
            }
        }

        $this->view->vente = $vente;
    }

    // Supprimer une vente
    public function deleteAction($id)
    {
        $vente = Vente::findFirstById_vente($id);
        if ($vente) {
            if ($vente->delete()) {
                $this->flashSession->success("Vente supprimée avec succès");
            } else {
                $this->flashSession->error("Erreur lors de la suppression de la vente");
            }
        } else {
            $this->flashSession->error("Vente non trouvée");
        }
        return $this->response->redirect('vente/index');
    }
}