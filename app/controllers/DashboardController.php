<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Vente;
use GestionStockVente\Models\Stock;
use GestionStockVente\Models\Client;
use GestionStockVente\Models\Produit;
use GestionStockVente\Models\Concerner;
use GestionStockVente\Models\Utilisateur;
use Phalcon\Mvc\Model\ResultsetInterface;

class DashboardController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Tableau de Bord');
    }

    public function indexAction()
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        $userId = $this->session->get('auth')['id'] ?? null;

        $totalSalesAmount = 0;
        $stockTotal = 0;
        $totalClients = 0;
        $processedRecentSales = [];
        $processedStockAlerts = [];
        $userStats = []; // Initialisé comme un tableau vide
        $annualSalesData = [];
        $topProductsData = [];

        try {
            // --- Calcul des statistiques générales (Ces totaux restent en haut) ---
            $totalSalesAmount = Concerner::sum([
                'column' => 'QUANTITE_VENDUE * PRIX_UNITAIRE_VENDUE'
            ]);
            $totalSalesAmount = $totalSalesAmount ?? 0;

            $stockTotal = Stock::sum(['column' => 'QUANTITE_STOCK']);
            $stockTotal = $stockTotal ?? 0;

            $totalClients = Client::count();

            // --- Statistiques basées sur le rôle de l'utilisateur (Maintenant, on récupère les listes) ---
            if ($role === 'admin') {
                $userStats['users'] = Utilisateur::find()->toArray();
            } elseif ($role === 'vendeur' && $userId) {
                // Récupérer les 5 dernières ventes de cet utilisateur
                $salesByUserResultSet = Vente::find([
                    'conditions' => 'ID_UTILISATEUR = :id:',
                    'bind' => ['id' => $userId],
                    'order' => 'DATE_VENTE DESC',
                    'limit' => 5
                ]);

                $processedSalesByUser = [];
                if ($salesByUserResultSet instanceof ResultsetInterface && $salesByUserResultSet->count() > 0) {
                    foreach ($salesByUserResultSet as $sale) {
                        $tempSale = $sale->toArray();
                        $client = $sale->getClient();
                        $tempSale['client_name'] = $client ? $client->NOM_CLIENT . ' ' . $client->PRENOM_CLIENT : 'Client inconnu';
                        $tempSale['date'] = date('d M Y H:i', strtotime($tempSale['DATE_VENTE']));

                        // Récupérer les détails des produits pour cette vente
                        $concernerItems = Concerner::find([
                            'conditions' => 'ID_VENTE = :id:',
                            'bind' => ['id' => $sale->ID_VENTE]
                        ]);

                        $productsInSale = [];
                        $totalSaleAmount = 0;
                        if ($concernerItems instanceof ResultsetInterface && $concernerItems->count() > 0) {
                            foreach ($concernerItems as $item) {
                                $product = $item->getProduit();
                                $productsInSale[] = [
                                    'name' => $product ? $product->NOM_PRODUIT : 'Produit inconnu',
                                    'qty' => $item->QUANTITE_VENDUE,
                                    'price' => $item->PRIX_UNITAIRE_VENDUE,
                                    'subtotal' => $item->QUANTITE_VENDUE * $item->PRIX_UNITAIRE_VENDUE
                                ];
                                $totalSaleAmount += ($item->QUANTITE_VENDUE * $item->PRIX_UNITAIRE_VENDUE);
                            }
                        }
                        $tempSale['products'] = $productsInSale;
                        $tempSale['total_amount'] = number_format($totalSaleAmount, 2) . ' €';
                        $processedSalesByUser[] = $tempSale;
                    }
                }
                $userStats['my_recent_sales'] = $processedSalesByUser;
            } elseif ($role === 'magasinier') {
                // Récupérer les 5 dernières mises à jour de stock
                $recentStockUpdatesResultSet = Stock::find([
                    'order' => 'DATE_MISEJOUR DESC',
                    'limit' => 5
                ]);

                $processedStockUpdates = [];
                if ($recentStockUpdatesResultSet instanceof ResultsetInterface && $recentStockUpdatesResultSet->count() > 0) {
                    foreach ($recentStockUpdatesResultSet as $stock) {
                        $tempStock = $stock->toArray();
                        $product = $stock->getProduit();
                        $tempStock['product_name'] = $product ? $product->NOM_PRODUIT : 'Produit inconnu';
                        $tempStock['date_formatted'] = date('d M Y H:i', strtotime($tempStock['DATE_MISEJOUR']));
                        $processedStockUpdates[] = $tempStock;
                    }
                }
                $userStats['recent_stock_updates'] = $processedStockUpdates;
            }

            // --- Récupération des transactions récentes (pour la table générale) ---
            $recentSalesResultSet = Vente::find([
                'order' => 'DATE_VENTE DESC',
                'limit' => 5
            ]);

            if ($recentSalesResultSet instanceof ResultsetInterface && $recentSalesResultSet->count() > 0) {
                foreach ($recentSalesResultSet as $sale) {
                    $tempSale = $sale->toArray();
                    $details = Concerner::findFirst([
                        'conditions' => 'ID_VENTE = :id:',
                        'bind' => ['id' => $sale->ID_VENTE]
                    ]);

                    $tempSale['product'] = 'N/A';
                    $tempSale['qty'] = 0;
                    $tempSale['amount'] = '0.00 €';
                    $tempSale['client'] = 'Inconnu';

                    if ($details) {
                        $product = $details->getProduit();
                        if ($product) {
                            $tempSale['product'] = $product->NOM_PRODUIT;
                        }

                        $client = $sale->getClient();
                        if ($client) {
                            $tempSale['client'] = $client->NOM_CLIENT . ' ' . $client->PRENOM_CLIENT; // Afficher nom complet du client
                        }

                        $tempSale['qty'] = $details->QUANTITE_VENDUE;
                        $tempSale['amount'] = number_format($details->QUANTITE_VENDUE * $details->PRIX_UNITAIRE_VENDUE, 2) . ' €';
                    }
                    $tempSale['date'] = date('d M Y', strtotime($tempSale['DATE_VENTE']));
                    $tempSale['time'] = date('H:i', strtotime($tempSale['DATE_VENTE']));
                    $processedRecentSales[] = $tempSale;
                }
            }

            // --- Récupération des alertes de stock ---
            $stockAlertsResultSet = Stock::find([
                'conditions' => 'QUANTITE_STOCK < 10',
                'order' => 'QUANTITE_STOCK ASC'
            ]);

            if ($stockAlertsResultSet instanceof ResultsetInterface && $stockAlertsResultSet->count() > 0) {
                foreach ($stockAlertsResultSet as $alert) {
                    $tempAlert = $alert->toArray();
                    $product = $alert->getProduit();
                    $tempAlert['name'] = $product ? $product->NOM_PRODUIT : 'Produit inconnu';

                    if ($tempAlert['QUANTITE_STOCK'] <= 2) {
                        $tempAlert['level'] = 'critique';
                        $tempAlert['level_class'] = 'text-red-500';
                        $tempAlert['bg_class'] = 'bg-red-100';
                        $tempAlert['text_class'] = 'text-red-600';
                    } elseif ($tempAlert['QUANTITE_STOCK'] <= 5) {
                        $tempAlert['level'] = 'bas';
                        $tempAlert['level_class'] = 'text-yellow-500';
                        $tempAlert['bg_class'] = 'bg-yellow-100';
                        $tempAlert['text_class'] = 'text-yellow-600';
                    } else {
                        $tempAlert['level'] = 'moyen';
                        $tempAlert['level_class'] = 'text-blue-500';
                        $tempAlert['bg_class'] = 'bg-blue-100';
                        $tempAlert['text_class'] = 'text-blue-600';
                    }
                    $processedStockAlerts[] = $tempAlert;
                }
            }

            // --- Préparation des données pour les graphiques (Requêtes Réelles) ---
            $currentYear = date('Y');
            $salesByMonth = $this->modelsManager->createQuery(
                "SELECT DATE_FORMAT(v.DATE_VENTE, '%b') as month_label, MONTH(v.DATE_VENTE) as month_num, SUM(c.QUANTITE_VENDUE * c.PRIX_UNITAIRE_VENDUE) as total_sales
                 FROM GestionStockVente\\Models\\Vente v
                 JOIN GestionStockVente\\Models\\Concerner c ON v.ID_VENTE = c.ID_VENTE
                 WHERE YEAR(v.DATE_VENTE) = :year:
                 GROUP BY month_label, month_num
                 ORDER BY month_num ASC"
            )->execute(['year' => $currentYear]);

            $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
            $annualSalesData = array_fill_keys($months, 0);

            if ($salesByMonth instanceof ResultsetInterface && $salesByMonth->count() > 0) {
                foreach ($salesByMonth as $row) {
                    $monthIndex = $row->month_num - 1;
                    if (isset($months[$monthIndex])) {
                        $annualSalesData[$months[$monthIndex]] = (float) $row->total_sales;
                    }
                }
            }
            $formattedAnnualSalesData = [];
            foreach ($annualSalesData as $month_label => $sales) {
                $formattedAnnualSalesData[] = ['month' => $month_label, 'sales' => $sales];
            }
            $annualSalesData = $formattedAnnualSalesData;

            $topProductsResult = $this->modelsManager->createQuery(
                "SELECT p.NOM_PRODUIT as product_name, SUM(c.QUANTITE_VENDUE * c.PRIX_UNITAIRE_VENDUE) as total_sold_amount
                 FROM GestionStockVente\\Models\\Concerner c
                 JOIN GestionStockVente\\Models\\Produit p ON c.ID_PRODUIT = p.ID_PRODUIT
                 GROUP BY p.NOM_PRODUIT
                 ORDER BY total_sold_amount DESC
                 LIMIT 5"
            )->execute();

            if ($topProductsResult instanceof ResultsetInterface && $topProductsResult->count() > 0) {
                $topProductsData = $topProductsResult->toArray();
            } else {
                $topProductsData = [];
            }
        } catch (\Exception $e) {
            echo "<div style='background-color: #ffe0e0; border: 1px solid #ff0000; padding: 15px; margin: 20px; font-family: monospace; color: #ff0000;'>";
            echo "<h2>Erreur de base de données ou de modèle !</h2>";
            echo "<p>Veuillez vérifier vos définitions de modèles (namespaces, noms de colonnes, noms de tables), les relations, et la structure de votre base de données.</p>";
            echo "<p>Message d'erreur : <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
            echo "<p>Fichier : " . htmlspecialchars($e->getFile()) . " (Ligne: " . htmlspecialchars($e->getLine()) . ")</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";

            $totalSalesAmount = 0;
            $stockTotal = 0;
            $totalClients = 0;
            $processedRecentSales = [];
            $processedStockAlerts = [];
            $userStats = [];
            $annualSalesData = [];
            $topProductsData = [];
        }

        $this->view->setVars([
            'total_sales' => number_format((float) $totalSalesAmount, 2) . ' €',

            'stock_total' => $stockTotal,
            'total_clients' => $totalClients,
            'user_stats' => $userStats,
            'recent_sales' => $processedRecentSales,
            'stock_alerts' => $processedStockAlerts,
            'annual_sales_data' => $annualSalesData,
            'top_products_data' => $topProductsData,
        ]);

        $this->view->pick('dashboard/index');
    }
}
