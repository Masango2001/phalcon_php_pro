use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class FournisseurController extends Controller
{
    // List all fournisseurs
    public function indexAction()
    {
        $fournisseurs = Fournisseur::find();
        $this->view->fournisseurs = $fournisseurs;
    }

    // Show a single fournisseur
    public function showAction($id)
    {
        $fournisseur = Fournisseur::findFirstByIdFournisseur($id);
        if (!$fournisseur) {
            $this->flashSession->error("Fournisseur non trouvé.");
            return $this->response->redirect('fournisseur/index');
        }
        $this->view->fournisseur = $fournisseur;
    }

    // Create a new fournisseur
    public function createAction()
    {
        if ($this->request->isPost()) {
            $fournisseur = new Fournisseur();
            $fournisseur->nom_complet_fournisseur = $this->request->getPost('nom_complet_fournisseur', 'string');
            $fournisseur->adresse_fournisseur = $this->request->getPost('adresse_fournisseur', 'string');
            $fournisseur->email_fournisseur = $this->request->getPost('email_fournisseur', 'email');
            $fournisseur->telephone_fournisseur = $this->request->getPost('telephone_fournisseur', 'string');

            if ($fournisseur->save()) {
                $this->flashSession->success("Fournisseur ajouté avec succès.");
                return $this->response->redirect('fournisseur/index');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout du fournisseur.");
            }
        }
    }

    // Edit an existing fournisseur
    public function editAction($id)
    {
        $fournisseur = Fournisseur::findFirstByIdFournisseur($id);
        if (!$fournisseur) {
            $this->flashSession->error("Fournisseur non trouvé.");
            return $this->response->redirect('fournisseur/index');
        }

        if ($this->request->isPost()) {
            $fournisseur->nom_complet_fournisseur = $this->request->getPost('nom_complet_fournisseur', 'string');
            $fournisseur->adresse_fournisseur = $this->request->getPost('adresse_fournisseur', 'string');
            $fournisseur->email_fournisseur = $this->request->getPost('email_fournisseur', 'email');
            $fournisseur->telephone_fournisseur = $this->request->getPost('telephone_fournisseur', 'string');

            if ($fournisseur->save()) {
                $this->flashSession->success("Fournisseur modifié avec succès.");
                return $this->response->redirect('fournisseur/index');
            } else {
                $this->flashSession->error("Erreur lors de la modification du fournisseur.");
            }
        }

        $this->view->fournisseur = $fournisseur;
    }

    // Delete a fournisseur
    public function deleteAction($id)
    {
        $fournisseur = Fournisseur::findFirstByIdFournisseur($id);
        if (!$fournisseur) {
            $this->flashSession->error("Fournisseur non trouvé.");
        } elseif ($fournisseur->delete()) {
            $this->flashSession->success("Fournisseur supprimé avec succès.");
        } else {
            $this->flashSession->error("Erreur lors de la suppression du fournisseur.");
        }
        return $this->response->redirect('fournisseur/index');
    }
}