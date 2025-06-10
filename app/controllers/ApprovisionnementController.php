use Phalcon\Mvc\Controller;

<?php


class ApprovisionnementController extends Controller
{
    // Affiche la liste des approvisionnements
    public function indexAction()
    {
        $approvisionnements = Approvisionnement::find([
            'order' => 'date_approvisionnement DESC'
        ]);
        $this->view->approvisionnements = $approvisionnements;
    }

    // Affiche le formulaire d'ajout
    public function newAction()
    {
        // Fournisseurs et produits pour les listes déroulantes
        $this->view->fournisseurs = Fournisseur::find();
        $this->view->produits = Produit::find();
    }

    // Traite l'ajout d'un approvisionnement
    public function createAction()
    {
        if ($this->request->isPost()) {
            $approvisionnement = new Approvisionnement();
            $approvisionnement->id_fournisseur = $this->request->getPost('id_fournisseur', 'int');
            $approvisionnement->id_produit = $this->request->getPost('id_produit', 'int');
            $approvisionnement->quantite_approvisionnement = $this->request->getPost('quantite_approvisionnement', 'int');
            $approvisionnement->prix_unitaire_achat = $this->request->getPost('prix_unitaire_achat', 'float');
            $approvisionnement->date_approvisionnement = $this->request->getPost('date_approvisionnement');

            if ($approvisionnement->save()) {
                $this->flashSession->success("Approvisionnement ajouté avec succès.");
                return $this->response->redirect('approvisionnement');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout de l'approvisionnement.");
            }
        }
        // Si erreur, on recharge le formulaire
        return $this->dispatcher->forward([
            'controller' => 'approvisionnement',
            'action' => 'new'
        ]);
    }

    // Affiche le formulaire d'édition
    public function editAction($id)
    {
        $approvisionnement = Approvisionnement::findFirstById_approvisionnement($id);
        if (!$approvisionnement) {
            $this->flashSession->error("Approvisionnement non trouvé.");
            return $this->response->redirect('approvisionnement');
        }
        $this->view->approvisionnement = $approvisionnement;
        $this->view->fournisseurs = Fournisseur::find();
        $this->view->produits = Produit::find();
    }

    // Traite la modification
    public function updateAction($id)
    {
        if ($this->request->isPost()) {
            $approvisionnement = Approvisionnement::findFirstById_approvisionnement($id);
            if (!$approvisionnement) {
                $this->flashSession->error("Approvisionnement non trouvé.");
                return $this->response->redirect('approvisionnement');
            }
            $approvisionnement->id_fournisseur = $this->request->getPost('id_fournisseur', 'int');
            $approvisionnement->id_produit = $this->request->getPost('id_produit', 'int');
            $approvisionnement->quantite_approvisionnement = $this->request->getPost('quantite_approvisionnement', 'int');
            $approvisionnement->prix_unitaire_achat = $this->request->getPost('prix_unitaire_achat', 'float');
            $approvisionnement->date_approvisionnement = $this->request->getPost('date_approvisionnement');

            if ($approvisionnement->save()) {
                $this->flashSession->success("Approvisionnement modifié avec succès.");
                return $this->response->redirect('approvisionnement');
            } else {
                $this->flashSession->error("Erreur lors de la modification.");
            }
        }
        // Si erreur, on recharge le formulaire
        return $this->dispatcher->forward([
            'controller' => 'approvisionnement',
            'action' => 'edit',
            'params' => [$id]
        ]);
    }

    // Supprime un approvisionnement
    public function deleteAction($id)
    {
        $approvisionnement = Approvisionnement::findFirstById_approvisionnement($id);
        if ($approvisionnement && $approvisionnement->delete()) {
            $this->flashSession->success("Approvisionnement supprimé.");
        } else {
            $this->flashSession->error("Erreur lors de la suppression.");
        }
        return $this->response->redirect('approvisionnement');
    }
}