use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class VendeurController extends Controller
{
    // Tableau de bord du vendeur
    public function dashboardAction()
    {
        // Récupérer les ventes, clients et produits concernés pour le dashboard
        $ventes = Vente::find();
        $clients = Client::find();
        $concerner = Concerner::find();

        $this->view->ventes = $ventes;
        $this->view->clients = $clients;
        $this->view->concerner = $concerner;
    }

    // Gestion des ventes (liste, ajout, modification, suppression)
    public function ventesAction()
    {
        $ventes = Vente::find();
        $this->view->ventes = $ventes;
    }

    public function ajouterVenteAction()
    {
        if ($this->request->isPost()) {
            $vente = new Vente();
            $vente->id_client = $this->request->getPost('id_client', 'int');
            $vente->id_utilisateur = $this->request->getPost('id_utilisateur', 'int');
            $vente->date_vente = $this->request->getPost('date_vente');
            $vente->id_produit = $this->request->getPost('id_produit', 'int');

            if ($vente->save()) {
                $this->flashSession->success("Vente ajoutée avec succès");
                return $this->response->redirect('vendeur/ventes');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout de la vente");
            }
        }
    }

    public function modifierVenteAction($id)
    {
        $vente = Vente::findFirstById_vente($id);
        if (!$vente) {
            $this->flashSession->error("Vente non trouvée");
            return $this->response->redirect('vendeur/ventes');
        }

        if ($this->request->isPost()) {
            $vente->id_client = $this->request->getPost('id_client', 'int');
            $vente->id_utilisateur = $this->request->getPost('id_utilisateur', 'int');
            $vente->date_vente = $this->request->getPost('date_vente');
            $vente->id_produit = $this->request->getPost('id_produit', 'int');

            if ($vente->save()) {
                $this->flashSession->success("Vente modifiée avec succès");
                return $this->response->redirect('vendeur/ventes');
            } else {
                $this->flashSession->error("Erreur lors de la modification de la vente");
            }
        }

        $this->view->vente = $vente;
    }

    public function supprimerVenteAction($id)
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
        return $this->response->redirect('vendeur/ventes');
    }

    // Gestion des clients (liste, ajout, modification, suppression)
    public function clientsAction()
    {
        $clients = Client::find();
        $this->view->clients = $clients;
    }

    public function ajouterClientAction()
    {
        if ($this->request->isPost()) {
            $client = new Client();
            $client->nom_client = $this->request->getPost('nom_client', 'string');
            $client->prenom_client = $this->request->getPost('prenom_client', 'string');
            $client->adresse_client = $this->request->getPost('adresse_client', 'string');
            $client->telephone_client = $this->request->getPost('telephone_client', 'string');

            if ($client->save()) {
                $this->flashSession->success("Client ajouté avec succès");
                return $this->response->redirect('vendeur/clients');
            } else {
                $this->flashSession->error("Erreur lors de l'ajout du client");
            }
        }
    }

    public function modifierClientAction($id)
    {
        $client = Client::findFirstById_client($id);
        if (!$client) {
            $this->flashSession->error("Client non trouvé");
            return $this->response->redirect('vendeur/clients');
        }

        if ($this->request->isPost()) {
            $client->nom_client = $this->request->getPost('nom_client', 'string');
            $client->prenom_client = $this->request->getPost('prenom_client', 'string');
            $client->adresse_client = $this->request->getPost('adresse_client', 'string');
            $client->telephone_client = $this->request->getPost('telephone_client', 'string');

            if ($client->save()) {
                $this->flashSession->success("Client modifié avec succès");
                return $this->response->redirect('vendeur/clients');
            } else {
                $this->flashSession->error("Erreur lors de la modification du client");
            }
        }

        $this->view->client = $client;
    }

    public function supprimerClientAction($id)
    {
        $client = Client::findFirstById_client($id);
        if ($client) {
            if ($client->delete()) {
                $this->flashSession->success("Client supprimé avec succès");
            } else {
                $this->flashSession->error("Erreur lors de la suppression du client");
            }
        } else {
            $this->flashSession->error("Client non trouvé");
        }
        return $this->response->redirect('vendeur/clients');
    }

    // Gestion des produits concernés par une vente
    public function concernerAction($id_vente)
    {
        $concerner = Concerner::find([
            'conditions' => 'id_vente = ?1',
            'bind' => [1 => $id_vente]
        ]);
        $this->view->concerner = $concerner;
        $this->view->id_vente = $id_vente;
    }

    public function ajouterConcernerAction($id_vente)
    {
        if ($this->request->isPost()) {
            $concerner = new Concerner();
            $concerner->id_vente = $id_vente;
            $concerner->id_produit = $this->request->getPost('id_produit', 'int');
            $concerner->quantite_vendue = $this->request->getPost('quantite_vendue', 'int');
            $concerner->prix_unitaire_vendue = $this->request->getPost('prix_unitaire_vendue', 'float');

            if ($concerner->save()) {
                $this->flashSession->success("Produit ajouté à la vente");
                return $this->response->redirect('vendeur/concerner/' . $id_vente);
            } else {
                $this->flashSession->error("Erreur lors de l'ajout du produit à la vente");
            }
        }
        $this->view->id_vente = $id_vente;
    }

    public function modifierConcernerAction($id_vente, $id_produit)
    {
        $concerner = Concerner::findFirst([
            'conditions' => 'id_vente = ?1 AND id_produit = ?2',
            'bind' => [1 => $id_vente, 2 => $id_produit]
        ]);
        if (!$concerner) {
            $this->flashSession->error("Produit concerné non trouvé");
            return $this->response->redirect('vendeur/concerner/' . $id_vente);
        }

        if ($this->request->isPost()) {
            $concerner->quantite_vendue = $this->request->getPost('quantite_vendue', 'int');
            $concerner->prix_unitaire_vendue = $this->request->getPost('prix_unitaire_vendue', 'float');

            if ($concerner->save()) {
                $this->flashSession->success("Produit concerné modifié");
                return $this->response->redirect('vendeur/concerner/' . $id_vente);
            } else {
                $this->flashSession->error("Erreur lors de la modification");
            }
        }

        $this->view->concerner = $concerner;
        $this->view->id_vente = $id_vente;
    }

    public function supprimerConcernerAction($id_vente, $id_produit)
    {
        $concerner = Concerner::findFirst([
            'conditions' => 'id_vente = ?1 AND id_produit = ?2',
            'bind' => [1 => $id_vente, 2 => $id_produit]
        ]);
        if ($concerner) {
            if ($concerner->delete()) {
                $this->flashSession->success("Produit supprimé de la vente");
            } else {
                $this->flashSession->error("Erreur lors de la suppression");
            }
        } else {
            $this->flashSession->error("Produit concerné non trouvé");
        }
        return $this->response->redirect('vendeur/concerner/' . $id_vente);
    }
}