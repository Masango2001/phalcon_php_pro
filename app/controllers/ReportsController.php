<?php

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Produit;
use GestionStockVente\Models\Stock;
use GestionStockVente\Models\Vente;
use GestionStockVente\Models\Fournisseur;
use GestionStockVente\Models\Concerner;
use GestionStockVente\Models\Approvisionnement;
use Phalcon\Db\RawValue;

class ReportsController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Rapports');
    }

    public function indexAction()
    {

        $this->view->setVar('defaultReportType', 'Rapport des ventes');
        $this->view->setVar('defaultDateRange', 'Ce mois-ci');
    }


    public function getReportDataAction() // Renamed for consistency within the ReportsController
    {
        $this->view->disable(); // Do not render a view for this API action

        // Ensure the request is POST and contains JSON
        if (!$this->request->isPost() || !$this->request->isJson()) {
            $this->response->setJsonContent(['status' => 'error', 'message' => 'Requête invalide ou format incorrect.']);
            return $this->response->send();
        }

        $jsonData = $this->request->getJsonRawBody();
        $reportType = $jsonData->report_type ?? null;
        $dateRange = $jsonData->date_range ?? null;
        // For custom dates, you'll need to add start_date and end_date fields in your frontend
        $startDateCustom = $jsonData->start_date ?? null;
        $endDateCustom = $jsonData->end_date ?? null;

        $data = [];
        $summary = [];
        $error = null;

        try {
            // Determine start and end dates based on the selected period
            list($startDate, $endDate) = $this->getDateRange($dateRange, $startDateCustom, $endDateCustom);

            switch ($reportType) {
                case 'Rapport des ventes':
                    // Total Sales (sum of PRIX_UNITAIRE_VENDUE * QUANTITE_VENDUE from CONCERNER)
                    $totalSalesResult = $this->modelsManager->createQuery(
                        "SELECT SUM(c.QUANTITE_VENDUE * c.PRIX_UNITAIRE_VENDUE) AS total_sales
                         FROM GestionStockVente\Models\Concerner c
                         JOIN GestionStockVente\Models\Vente v ON c.ID_VENTE = v.ID_VENTE
                         WHERE v.DATE_VENTE BETWEEN :start_date: AND :end_date:"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate])->getFirst();

                    $totalSales = $totalSalesResult ? (float) $totalSalesResult->total_sales : 0;
                    $summary['totalSales'] = number_format($totalSales, 2, ',', ' ') . ' €';

                    // Number of Orders/Sales
                    $orderCount = Vente::count([
                        'conditions' => 'DATE_VENTE BETWEEN :start_date: AND :end_date:',
                        'bind' => ['start_date' => $startDate, 'end_date' => $endDate]
                    ]);
                    $summary['orderCount'] = $orderCount;

                    // Top 5 Bestselling Products (by QUANTITE_VENDUE)
                    $mostSoldProducts = $this->modelsManager->createQuery(
                        "SELECT p.NOM_PRODUIT, SUM(c.QUANTITE_VENDUE) AS total_quantity_sold
                         FROM GestionStockVente\Models\Concerner c
                         JOIN GestionStockVente\Models\Produit p ON c.ID_PRODUIT = p.ID_PRODUIT
                         JOIN GestionStockVente\Models\Vente v ON c.ID_VENTE = v.ID_VENTE
                         WHERE v.DATE_VENTE BETWEEN :start_date: AND :end_date:
                         GROUP BY p.NOM_PRODUIT
                         ORDER BY total_quantity_sold DESC
                         LIMIT 5"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate]);

                    $summary['mostSoldProducts'] = [];
                    foreach ($mostSoldProducts as $item) {
                        $summary['mostSoldProducts'][] = $item->NOM_PRODUIT . ' (' . $item->total_quantity_sold . ' unités)';
                    }
                    if (empty($summary['mostSoldProducts'])) {
                        $summary['mostSoldProducts'][] = 'Aucune donnée pour les produits vendus.';
                    }
                    $summary['mostSoldProducts'] = implode(', ', $summary['mostSoldProducts']);

                    // Data for daily/monthly sales chart
                    $dailySales = $this->modelsManager->createQuery(
                        "SELECT DATE_FORMAT(DATE_VENTE, '%Y-%m-%d') as sale_date, SUM(c.QUANTITE_VENDUE * c.PRIX_UNITAIRE_VENDUE) as total_daily_sales
                         FROM GestionStockVente\Models\Vente v
                         JOIN GestionStockVente\Models\Concerner c ON v.ID_VENTE = c.ID_VENTE
                         WHERE v.DATE_VENTE BETWEEN :start_date: AND :end_date:
                         GROUP BY sale_date
                         ORDER BY sale_date ASC"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate]);

                    $chartLabels = [];
                    $chartData = [];
                    foreach ($dailySales as $day) {
                        $chartLabels[] = $day->sale_date;
                        $chartData[] = (float) $day->total_daily_sales;
                    }

                    $data = [
                        'type' => 'bar',
                        'labels' => $chartLabels,
                        'datasets' => [[
                            'label' => 'Ventes (€)',
                            'backgroundColor' => '#4CAF50', // Green for sales
                            'borderColor' => '#4CAF50',
                            'data' => $chartData
                        ]]
                    ];
                    break;

                case 'Rapport de stock':
                    // Total number of products
                    $summary['totalProducts'] = Produit::count();

                    // Products in stock (summing QUANTITE_STOCK for each product)
                    $productsWithStock = $this->modelsManager->createQuery(
                        "SELECT p.NOM_PRODUIT, SUM(s.QUANTITE_STOCK) AS current_stock
                         FROM GestionStockVente\Models\Produit p
                         JOIN GestionStockVente\Models\Stock s ON p.ID_PRODUIT = s.ID_PRODUIT
                         GROUP BY p.NOM_PRODUIT
                         HAVING current_stock > 0
                         ORDER BY p.NOM_PRODUIT ASC"
                    )->execute();

                    $summary['productsInStock'] = 0;
                    $tableData = [];
                    foreach ($productsWithStock as $prod) {
                        $summary['productsInStock'] += $prod->current_stock; // Sum of quantities in stock
                        $tableData[] = [
                            'name' => $prod->NOM_PRODUIT,
                            'stock' => $prod->current_stock
                        ];
                    }

                    // Products out of stock (QUANTITE_STOCK = 0)
                    $productsOutOfStockCount = $this->modelsManager->createQuery(
                        "SELECT COUNT(p.ID_PRODUIT) AS count_out_of_stock
                         FROM GestionStockVente\Models\Produit p
                         LEFT JOIN GestionStockVente\Models\Stock s ON p.ID_PRODUIT = s.ID_PRODUIT
                         GROUP BY p.ID_PRODUIT
                         HAVING SUM(s.QUANTITE_STOCK) = 0 OR SUM(s.QUANTITE_STOCK) IS NULL"
                    )->execute()->getFirst()->count_out_of_stock;
                    $summary['productsOutOfStock'] = $productsOutOfStockCount;

                    $data = [
                        'type' => 'table', // Display a table for stock report
                        'headers' => ['Produit', 'Stock Actuel'],
                        'rows' => $tableData
                    ];
                    break;

                case 'Rapport des fournisseurs':
                    // Total number of suppliers
                    $summary['totalSuppliers'] = Fournisseur::count();

                    // Supply amount per supplier
                    $supplierPurchases = $this->modelsManager->createQuery(
                        "SELECT f.NOM_COMPLET_FOURNISSEUR, SUM(a.QUANTITE_APPROVIONNEMENT * a.PRIX_UNITAIRE_ACHAT) AS total_purchases
                         FROM GestionStockVente\Models\Approvisionnement a
                         JOIN GestionStockVente\Models\Fournisseur f ON a.ID_FOURNISSEUR = f.ID_FOURNISSEUR
                         WHERE a.DATE_APPROVISIONNEMENT BETWEEN :start_date: AND :end_date:
                         GROUP BY f.NOM_COMPLET_FOURNISSEUR
                         ORDER BY total_purchases DESC"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate]);

                    $tableData = [];
                    foreach ($supplierPurchases as $sp) {
                        $tableData[] = [
                            'name' => $sp->NOM_COMPLET_FOURNISSEUR,
                            'total_purchases' => number_format((float)$sp->total_purchases, 2, ',', ' ') . ' €'
                        ];
                    }

                    $data = [
                        'type' => 'table',
                        'headers' => ['Fournisseur', 'Montant Total des Approvisionnements'],
                        'rows' => $tableData
                    ];
                    break;

                case 'Rapport financier':
                    // Total Revenues (sales)
                    $totalRevenuesResult = $this->modelsManager->createQuery(
                        "SELECT SUM(c.QUANTITE_VENDUE * c.PRIX_UNITAIRE_VENDUE) AS total_revenues
                         FROM GestionStockVente\Models\Concerner c
                         JOIN GestionStockVente\Models\Vente v ON c.ID_VENTE = v.ID_VENTE
                         WHERE v.DATE_VENTE BETWEEN :start_date: AND :end_date:"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate])->getFirst();

                    $totalRevenues = $totalRevenuesResult ? (float) $totalRevenuesResult->total_revenues : 0;
                    $summary['totalRevenues'] = number_format($totalRevenues, 2, ',', ' ') . ' €';

                    // Total Expenses (supplies/purchases)
                    $totalExpensesResult = $this->modelsManager->createQuery(
                        "SELECT SUM(a.QUANTITE_APPROVIONNEMENT * a.PRIX_UNITAIRE_ACHAT) AS total_expenses
                         FROM GestionStockVente\Models\Approvisionnement a
                         WHERE a.DATE_APPROVISIONNEMENT BETWEEN :start_date: AND :end_date:"
                    )->execute(['start_date' => $startDate, 'end_date' => $endDate])->getFirst();

                    $totalExpenses = $totalExpensesResult ? (float) $totalExpensesResult->total_expenses : 0;
                    $summary['totalExpenses'] = number_format($totalExpenses, 2, ',', ' ') . ' €';

                    $netProfit = $totalRevenues - $totalExpenses;
                    $summary['netProfit'] = number_format($netProfit, 2, ',', ' ') . ' €';

                    $data = [
                        'type' => 'pie', // Pie chart for revenues/expenses
                        'labels' => ['Revenus', 'Dépenses'],
                        'datasets' => [[
                            'label' => 'Financier',
                            'data' => [$totalRevenues, $totalExpenses],
                            'backgroundColor' => ['#4CAF50', '#FFC107'], // Green for revenues, Yellow for expenses
                            'hoverOffset' => 4
                        ]]
                    ];
                    break;

                default:
                    $error = "Type de rapport inconnu.";
                    break;
            }
        } catch (\Exception $e) {
            $error = "Erreur lors de la génération du rapport : " . $e->getMessage();
            // In a production environment, you would log this error.
            // For example: $this->logger->error($error); if you have a logger service configured.
        }

        if ($error) {
            $this->response->setJsonContent(['status' => 'error', 'message' => $error]);
        } else {
            $this->response->setJsonContent([
                'status' => 'success',
                'summary' => $summary,
                'data' => $data
            ]);
        }

        return $this->response->send();
    }

    /**
     * Helper to determine start and end dates based on the selected period.
     * @param string $range The selected period (e.g., 'Aujourd\'hui', 'Ce mois-ci').
     * @param string|null $startDateCustom Custom start date (YYYY-MM-DD format).
     * @param string|null $endDateCustom Custom end date (YYYY-MM-DD format).
     * @return array Array containing [startDate, endDate].
     */
    private function getDateRange($range, $startDateCustom = null, $endDateCustom = null)
    {
        $startDate = date('Y-m-d 00:00:00');
        $endDate = date('Y-m-d 23:59:59');

        switch ($range) {
            case 'Aujourd\'hui':
                // Already set to today
                break;
            case 'Cette semaine':
                $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $endDate = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                break;
            case 'Ce mois-ci':
                $startDate = date('Y-m-01 00:00:00');
                $endDate = date('Y-m-t 23:59:59'); // 't' returns the number of days in the month
                break;
            case 'Cette année':
                $startDate = date('Y-01-01 00:00:00');
                $endDate = date('Y-12-31 23:59:59');
                break;
            case 'Personnalisé':
                // If you add date fields to the frontend for the custom period
                if ($startDateCustom && $endDateCustom) {
                    $startDate = date('Y-m-d 00:00:00', strtotime($startDateCustom));
                    $endDate = date('Y-m-d 23:59:59', strtotime($endDateCustom));
                }
                break;
            default:
                // Fallback or error if the range is not recognized
                break;
        }
        return [$startDate, $endDate];
    }
}
