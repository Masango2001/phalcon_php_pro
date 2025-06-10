use Phalcon\Mvc\Controller;

<?php


class StockController extends Controller
{
    // List all stocks
    public function indexAction()
    {
        $stocks = Stock::find();
        $this->view->stocks = $stocks;
    }

    // Show a single stock by ID
    public function showAction($id)
    {
        $stock = Stock::findFirstById_stock($id);
        if (!$stock) {
            $this->flashSession->error("Stock not found");
            return $this->response->redirect('stock/index');
        }
        $this->view->stock = $stock;
    }

    // Create a new stock
    public function createAction()
    {
        if ($this->request->isPost()) {
            $stock = new Stock();
            $stock->id_produit = $this->request->getPost('id_produit', 'int');
            $stock->quantite_stock = $this->request->getPost('quantite_stock', 'int');
            $stock->date_Misejour = date('Y-m-d H:i:s');

            if ($stock->save()) {
                $this->flashSession->success("Stock created successfully");
                return $this->response->redirect('stock/index');
            } else {
                $this->flashSession->error("Failed to create stock");
            }
        }
    }

    // Edit an existing stock
    public function editAction($id)
    {
        $stock = Stock::findFirstById_stock($id);
        if (!$stock) {
            $this->flashSession->error("Stock not found");
            return $this->response->redirect('stock/index');
        }

        if ($this->request->isPost()) {
            $stock->id_produit = $this->request->getPost('id_produit', 'int');
            $stock->quantite_stock = $this->request->getPost('quantite_stock', 'int');
            $stock->date_Misejour = date('Y-m-d H:i:s');

            if ($stock->save()) {
                $this->flashSession->success("Stock updated successfully");
                return $this->response->redirect('stock/index');
            } else {
                $this->flashSession->error("Failed to update stock");
            }
        }

        $this->view->stock = $stock;
    }
}