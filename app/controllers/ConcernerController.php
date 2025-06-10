use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class ConcernerController extends Controller
{
    // List all records
    public function indexAction()
    {
        $concerner = Concerner::find();
        $this->view->concerner = $concerner;
    }

    // Show a single record
    public function showAction($id_vente, $id_produit)
    {
        $concerner = Concerner::findFirst([
            'conditions' => 'id_vente = ?1 AND id_produit = ?2',
            'bind' => [
                1 => $id_vente,
                2 => $id_produit
            ]
        ]);
        if (!$concerner) {
            $this->flashSession->error("Concerner not found");
            return $this->response->redirect('concerner/index');
        }
        $this->view->concerner = $concerner;
    }

    // Create a new record
    public function createAction()
    {
        if ($this->request->isPost()) {
            $concerner = new Concerner();
            $concerner->id_vente = $this->request->getPost('id_vente', 'int');
            $concerner->id_produit = $this->request->getPost('id_produit', 'int');
            $concerner->quantite_vendue = $this->request->getPost('quantite_vendue', 'int');
            $concerner->prix_unitaire_vendue = $this->request->getPost('prix_unitaire_vendue', 'float');

            if ($concerner->save()) {
                $this->flashSession->success("Concerner created successfully");
                return $this->response->redirect('concerner/index');
            } else {
                $this->flashSession->error("Failed to create Concerner");
            }
        }
    }

    // Edit an existing record
    public function editAction($id_vente, $id_produit)
    {
        $concerner = Concerner::findFirst([
            'conditions' => 'id_vente = ?1 AND id_produit = ?2',
            'bind' => [
                1 => $id_vente,
                2 => $id_produit
            ]
        ]);
        if (!$concerner) {
            $this->flashSession->error("Concerner not found");
            return $this->response->redirect('concerner/index');
        }

        if ($this->request->isPost()) {
            $concerner->quantite_vendue = $this->request->getPost('quantite_vendue', 'int');
            $concerner->prix_unitaire_vendue = $this->request->getPost('prix_unitaire_vendue', 'float');

            if ($concerner->save()) {
                $this->flashSession->success("Concerner updated successfully");
                return $this->response->redirect('concerner/index');
            } else {
                $this->flashSession->error("Failed to update Concerner");
            }
        }

        $this->view->concerner = $concerner;
    }

    // Delete a record
    public function deleteAction($id_vente, $id_produit)
    {
        $concerner = Concerner::findFirst([
            'conditions' => 'id_vente = ?1 AND id_produit = ?2',
            'bind' => [
                1 => $id_vente,
                2 => $id_produit
            ]
        ]);
        if ($concerner) {
            if ($concerner->delete()) {
                $this->flashSession->success("Concerner deleted successfully");
            } else {
                $this->flashSession->error("Failed to delete Concerner");
            }
        } else {
            $this->flashSession->error("Concerner not found");
        }
        return $this->response->redirect('concerner/index');
    }
}