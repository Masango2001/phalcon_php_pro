<?php
// app/controllers/VentesController.php

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Vente;
use GestionStockVente\Models\Concerner;
use GestionStockVente\Models\Produit;
use GestionStockVente\Models\Client;
use GestionStockVente\Models\Categorie;

class VentesController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Ventes');
    }

    public function indexAction()
    {
        // Code existant inchangé
        $salesTodayCount = 0;
        $salesTodayTotal = 0;
        $salesYesterdayTotal = 0;
        $salesMonthCount = 0;
        $salesMonthTotal = 0;
        $salesPreviousMonthTotal = 0;

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $startOfPreviousMonth = date('Y-m-01', strtotime('-1 month'));
        $endOfPreviousMonth = date('Y-m-t', strtotime('-1 month'));

        $ventesToday = Vente::find([
            "conditions" => "DATE_VENTE LIKE :today:",
            "bind" => ["today" => $today . '%']
        ]);
        foreach ($ventesToday as $vente) {
            $salesTodayCount++;
            $salesTodayTotal += $this->getSaleTotal($vente->ID_VENTE);
        }

        $ventesYesterday = Vente::find([
            "conditions" => "DATE_VENTE LIKE :yesterday:",
            "bind" => ["yesterday" => $yesterday . '%']
        ]);
        foreach ($ventesYesterday as $vente) {
            $salesYesterdayTotal += $this->getSaleTotal($vente->ID_VENTE);
        }

        $salesTodayChange = 0;
        if ($salesYesterdayTotal > 0) {
            $salesTodayChange = (($salesTodayTotal - $salesYesterdayTotal) / $salesYesterdayTotal) * 100;
        } elseif ($salesTodayTotal > 0) {
            $salesTodayChange = 100;
        }

        $ventesMonth = Vente::find([
            "conditions" => "DATE_VENTE BETWEEN :start_of_month: AND :end_of_month:",
            "bind" => [
                "start_of_month" => $startOfMonth . ' 00:00:00',
                "end_of_month" => $endOfMonth . ' 23:59:59'
            ]
        ]);
        foreach ($ventesMonth as $vente) {
            $salesMonthCount++;
            $salesMonthTotal += $this->getSaleTotal($vente->ID_VENTE);
        }

        $ventesPreviousMonth = Vente::find([
            "conditions" => "DATE_VENTE BETWEEN :start_of_previous_month: AND :end_of_previous_month:",
            "bind" => [
                "start_of_previous_month" => $startOfPreviousMonth . ' 00:00:00',
                "end_of_previous_month" => $endOfPreviousMonth . ' 23:59:59'
            ]
        ]);
        foreach ($ventesPreviousMonth as $vente) {
            $salesPreviousMonthTotal += $this->getSaleTotal($vente->ID_VENTE);
        }

        $salesMonthChange = 0;
        if ($salesPreviousMonthTotal > 0) {
            $salesMonthChange = (($salesMonthTotal - $salesPreviousMonthTotal) / $salesPreviousMonthTotal) * 100;
        } elseif ($salesMonthTotal > 0) {
            $salesMonthChange = 100;
        }

        $sales = [];
        $allVentes = Vente::find([
            "order" => "DATE_VENTE DESC",
            "limit" => 10
        ]);

        foreach ($allVentes as $vente) {
            $client = Client::findFirst($vente->ID_CLIENT);
            $productsInSale = [];
            $totalSaleAmount = 0;

            $concernerItems = Concerner::find([
                "conditions" => "ID_VENTE = :id_vente:",
                "bind" => ["id_vente" => $vente->ID_VENTE]
            ]);

            foreach ($concernerItems as $item) {
                $produit = Produit::findFirst($item->ID_PRODUIT);
                if ($produit) {
                    $productsInSale[] = [
                        'name' => $produit->NOM_PRODUIT,
                    ];
                }
                $totalSaleAmount += ($item->QUANTITE_VENDUE * $item->PRIX_UNITAIRE_VENDUE);
            }

            $sales[] = [
                'id' => $vente->ID_VENTE,
                'reference' => 'SALE-' . str_pad($vente->ID_VENTE, 3, '0', STR_PAD_LEFT),
                'date' => $vente->DATE_VENTE,
                'client' => [
                    'name' => $client ? $client->NOM_CLIENT . ' ' . $client->PRENOM_CLIENT : 'N/A',
                    'contact' => $client && property_exists($client, 'EMAIL_CLIENT') ? $client->EMAIL_CLIENT : ($client ? $client->TELEPHONE_CLIENT : 'N/A')
                ],
                'products' => $productsInSale,
                'total' => $totalSaleAmount,
                'timeAgo' => $this->getTimeAgo($vente->DATE_VENTE)
            ];
        }

        $salesTrend = $this->getMonthlySalesData();
        $salesDistributionByCategory = $this->getSalesDistributionByCategory();
        $recentSales = array_slice($sales, 0, 3);
        $bestCustomers = $this->getBestCustomers();

        $this->view->setVars([
            'salesTodayCount' => $salesTodayCount,
            'salesTodayTotal' => number_format($salesTodayTotal, 2, '.', ''),
            'salesTodayChange' => number_format($salesTodayChange, 1, '.', ''),
            'salesMonthCount' => $salesMonthCount,
            'salesMonthTotal' => number_format($salesMonthTotal, 2, '.', ''),
            'salesMonthChange' => number_format($salesMonthChange, 1, '.', ''),
            'sales' => $sales,
            'salesTrendData' => json_encode($salesTrend['data']),
            'salesTrendLabels' => json_encode($salesTrend['labels']),
            'salesDistributionData' => json_encode($salesDistributionByCategory['data']),
            'salesDistributionLabels' => json_encode($salesDistributionByCategory['labels']),
            'recentSales' => $recentSales,
            'bestCustomers' => $bestCustomers,
        ]);
        $this->view->pick("sales/index");
    }

    public function viewAction($id)
    {
        $vente = Vente::findFirst($id);
        if (!$vente) {
            $this->flash->error("Vente non trouvée.");
            return $this->response->redirect('/sales');
        }

        $totalAmount = 0;
        $concerners = Concerner::find([
            "conditions" => "ID_VENTE = :id_vente:",
            "bind" => ["id_vente" => $vente->ID_VENTE]
        ]);

        foreach ($concerners as $concerner) {
            $concerner->p = Produit::findFirst($concerner->ID_PRODUIT); // Ajout de la relation produit
            $totalAmount += ($concerner->QUANTITE_VENDUE * $concerner->PRIX_UNITAIRE_VENDUE);
        }

        // Récupérer l'utilisateur connecté pour vérifier les permissions
        $currentUser = $this->session->get('auth');
        $userRole = $currentUser['role'] ?? 'Vendeur';
        $currentUserId = $currentUser['id'] ?? null;

        $this->view->setVars([
            'vente' => $vente,
            'concerners' => $concerners,
            'totalAmount' => $totalAmount,
            'userRole' => $userRole,
            'currentUserId' => $currentUserId,
            'saleStatus' => $this->getSaleStatus($vente->DATE_VENTE, $totalAmount)
        ]);
        $this->view->pick("sales/view");
    }

    private function getSaleStatus(string $dateVente, float $totalAmount): string
    {
        $saleDate = new \DateTime($dateVente);
        $now = new \DateTime();
        $diff = $now->diff($saleDate);

        if ($diff->days <= 1) {
            return 'Récente';
        } elseif ($diff->days > 7 && $totalAmount < 50) {
            return 'En attente';
        }
        return 'Complétée';
    }

    private function getSaleTotal(int $idVente): float
    {
        $total = 0;
        $concernerItems = Concerner::find([
            "conditions" => "ID_VENTE = :id_vente:",
            "bind" => ["id_vente" => $idVente]
        ]);
        foreach ($concernerItems as $item) {
            $total += ($item->QUANTITE_VENDUE * $item->PRIX_UNITAIRE_VENDUE);
        }
        return $total;
    }

    private function getTimeAgo($datetime)
    {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->y > 0) return $diff->y . ' an(s) ago';
        if ($diff->m > 0) return $diff->m . ' mois ago';
        if ($diff->d > 0) return $diff->d . ' jour(s) ago';
        if ($diff->h > 0) return $diff->h . ' heure(s) ago';
        if ($diff->i > 0) return $diff->i . ' minute(s) ago';
        return 'à l\'instant';
    }

    private function getMonthlySalesData()
    {
        $monthlySales = [];
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthName = date('M Y', strtotime("-$i months"));
            $startOfMonth = date('Y-m-01', strtotime("-$i months"));
            $endOfMonth = date('Y-m-t', strtotime("-$i months"));

            $builder = $this->modelsManager->createBuilder()
                ->columns('SUM(co.QUANTITE_VENDUE * co.PRIX_UNITAIRE_VENDUE) as total_sales')
                ->from(['v' => Vente::class])
                ->join(Concerner::class, 'v.ID_VENTE = co.ID_VENTE', 'co')
                ->where(
                    "v.DATE_VENTE BETWEEN :start_of_month: AND :end_of_month:",
                    [
                        "start_of_month" => $startOfMonth . ' 00:00:00',
                        "end_of_month"   => $endOfMonth . ' 23:59:59'
                    ]
                );

            $result = $builder->getQuery()->getSingleResult();
            $totalMonthSales = $result->total_sales ?? 0;

            $labels[] = $monthName;
            $data[] = (float)$totalMonthSales;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getSalesDistributionByCategory()
    {
        $categorySales = [];
        $labels = [];
        $data = [];

        $builder = $this->modelsManager->createBuilder()
            ->columns('c.NOM_CATEGORIE, SUM(co.QUANTITE_VENDUE * co.PRIX_UNITAIRE_VENDUE) as total_sales')
            ->from(['v' => Vente::class])
            ->join(Concerner::class, 'v.ID_VENTE = co.ID_VENTE', 'co')
            ->join(Produit::class, 'co.ID_PRODUIT = p.ID_PRODUIT', 'p')
            ->join(Categorie::class, 'p.ID_CATEGORIE = c.ID_CATEGORIE', 'c')
            ->groupBy('c.NOM_CATEGORIE')
            ->orderBy('total_sales DESC');

        $result = $builder->getQuery()->execute();

        foreach ($result as $row) {
            $labels[] = $row->NOM_CATEGORIE;
            $data[] = (float)$row->total_sales;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getBestCustomers()
    {
        $bestCustomers = [];

        $builder = $this->modelsManager->createBuilder()
            ->columns('cl.NOM_CLIENT, cl.PRENOM_CLIENT, cl.TELEPHONE_CLIENT, SUM(co.QUANTITE_VENDUE * co.PRIX_UNITAIRE_VENDUE) as total_spent, COUNT(DISTINCT v.ID_VENTE) as purchase_count')
            ->from(['v' => Vente::class])
            ->join(Client::class, 'v.ID_CLIENT = cl.ID_CLIENT', 'cl')
            ->join(Concerner::class, 'v.ID_VENTE = co.ID_VENTE', 'co')
            ->groupBy('cl.ID_CLIENT, cl.NOM_CLIENT, cl.PRENOM_CLIENT, cl.TELEPHONE_CLIENT')
            ->orderBy('total_spent DESC')
            ->limit(3);

        $result = $builder->getQuery()->execute();

        foreach ($result as $row) {
            $bestCustomers[] = [
                'name' => $row->NOM_CLIENT . ' ' . $row->PRENOM_CLIENT,
                'contact' => property_exists($row, 'EMAIL_CLIENT') ? $row->EMAIL_CLIENT : $row->TELEPHONE_CLIENT,
                'totalSpent' => (float)$row->total_spent,
                'purchaseCount' => (int)$row->purchase_count
            ];
        }

        return $bestCustomers;
    }
}
