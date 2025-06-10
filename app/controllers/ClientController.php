use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

<?php


class ClientController extends Controller
{
    // List all clients
    public function indexAction()
    {
        $clients = Client::find();
        $this->view->clients = $clients;
    }

    // Show a single client
    public function showAction($id)
    {
        $client = Client::findFirstById_client($id);
        if (!$client) {
            $this->flashSession->error("Client not found");
            return $this->response->redirect('client/index');
        }
        $this->view->client = $client;
    }

    // Create a new client
    public function createAction()
    {
        if ($this->request->isPost()) {
            $client = new Client();
            $client->nom_client = $this->request->getPost('nom_client', 'string');
            $client->prenom_client = $this->request->getPost('prenom_client', 'string');
            $client->adresse_client = $this->request->getPost('adresse_client', 'string');
            $client->telephone_client = $this->request->getPost('telephone_client', 'string');

            if ($client->save()) {
                $this->flashSession->success("Client created successfully");
                return $this->response->redirect('client/index');
            } else {
                $this->flashSession->error("Failed to create client");
            }
        }
    }

    // Edit an existing client
    public function editAction($id)
    {
        $client = Client::findFirstById_client($id);
        if (!$client) {
            $this->flashSession->error("Client not found");
            return $this->response->redirect('client/index');
        }

        if ($this->request->isPost()) {
            $client->nom_client = $this->request->getPost('nom_client', 'string');
            $client->prenom_client = $this->request->getPost('prenom_client', 'string');
            $client->adresse_client = $this->request->getPost('adresse_client', 'string');
            $client->telephone_client = $this->request->getPost('telephone_client', 'string');

            if ($client->save()) {
                $this->flashSession->success("Client updated successfully");
                return $this->response->redirect('client/index');
            } else {
                $this->flashSession->error("Failed to update client");
            }
        }

        $this->view->client = $client;
    }

    // Delete a client
    public function deleteAction($id)
    {
        $client = Client::findFirstById_client($id);
        if ($client) {
            if ($client->delete()) {
                $this->flashSession->success("Client deleted successfully");
            } else {
                $this->flashSession->error("Failed to delete client");
            }
        } else {
            $this->flashSession->error("Client not found");
        }
        return $this->response->redirect('client/index');
    }
}