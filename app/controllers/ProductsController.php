<?php

declare(strict_types=1);

namespace GestionStockVente\Controllers;

use Phalcon\Mvc\Controller;
use GestionStockVente\Models\Produit;
use GestionStockVente\Models\Stock;
use GestionStockVente\Models\Concerner;
use GestionStockVente\Models\Categorie;
use Phalcon\Mvc\Model\ResultsetInterface;

class ProductsController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->setVar('title', 'Gestion des Produits');
    }

    public function indexAction()
    {
        // Récupération du rôle de l'utilisateur depuis la session, comme dans DashboardController
        // Assurez-vous que le rôle est bien stocké dans $_SESSION['auth']['role']
        $role = $this->session->get('auth')['role'] ?? 'guest';

        // Initialisation des variables de permissions
        $canAddProducts = false;
        $canViewProductsList = false;
        $canModifyProducts = false;
        $canDeleteProducts = false;

        // Définition des permissions en fonction du rôle
        switch ($role) {
            case 'admin':
                $canAddProducts = true;
                $canViewProductsList = true;
                $canModifyProducts = true;
                $canDeleteProducts = true;
                break;
            case 'magasinier':
                $canAddProducts = true;
                $canViewProductsList = true;
                $canModifyProducts = true;
                $canDeleteProducts = true; // Un magasinier peut supprimer des produits
                break;
            case 'vendeur':
                // Le vendeur a uniquement un accès en lecture à la liste des produits
                $canViewProductsList = true;
                $canAddProducts = false;
                $canModifyProducts = false;
                $canDeleteProducts = false;
                break;
            default:
                // Pour les invités ou rôles inconnus, aucune permission
                $canViewProductsList = false;
                break;
        }

        $productsData = []; // Initialisation du tableau des produits
        try {
            if ($canViewProductsList) {
                // Récupération de tous les produits avec leurs catégories et stocks associés
                // Nous utilisons `find()` et eager loading via `join` dans le query builder si nécessaire,
                // ou simplement en accédant aux relations. Pour cet affichage, un query builder simple suffit.

                // Récupérer les produits en incluant la catégorie et le stock
                $produitsResultSet = $this->modelsManager->createQuery(
                    "SELECT p.ID_PRODUIT, p.NOM_PRODUIT, c.NOM_CATEGORIE, s.QUANTITE_STOCK
                     FROM GestionStockVente\\Models\\Produit p
                     LEFT JOIN GestionStockVente\\Models\\Categorie c ON p.ID_CATEGORIE = c.ID_CATEGORIE
                     LEFT JOIN GestionStockVente\\Models\\Stock s ON p.ID_PRODUIT = s.ID_PRODUIT
                     ORDER BY p.NOM_PRODUIT ASC" // Ajout d'un tri par défaut
                )->execute();

                if ($produitsResultSet instanceof ResultsetInterface && $produitsResultSet->count() > 0) {
                    foreach ($produitsResultSet as $row) {
                        $productId = $row->ID_PRODUIT;

                        // Calcul du prix de vente moyen pour ce produit
                        // Ce prix vient de la table `Concerner` (détails des ventes)
                        $averagePriceResult = $this->modelsManager->createQuery(
                            "SELECT AVG(c.PRIX_UNITAIRE_VENDUE) as average_price
                             FROM GestionStockVente\\Models\\Concerner c
                             WHERE c.ID_PRODUIT = :productId:"
                        )->execute(['productId' => $productId]);

                        $averagePrice = 'N/A'; // Par défaut si aucun prix de vente n'est trouvé
                        if ($averagePriceResult instanceof ResultsetInterface && $averagePriceResult->count() > 0) {
                            $priceRow = $averagePriceResult->getFirst();
                            if ($priceRow && $priceRow->average_price !== null) {
                                $averagePrice = number_format((float) $priceRow->average_price, 2, ',', ' ') . ' Fbu';
                            }
                        }

                        // Détermination du statut basé sur la quantité de stock
                        $stockQuantity = $row->QUANTITE_STOCK ?? 0; // Utiliser 0 si QUANTITE_STOCK est null
                        $statutProduit = 'Disponible';
                        if ($stockQuantity <= 5 && $stockQuantity > 0) {
                            $statutProduit = 'Faible Stock';
                        } elseif ($stockQuantity === 0) {
                            $statutProduit = 'Rupture de Stock';
                        }

                        $productsData[] = [
                            'ID_PRODUIT' => $row->ID_PRODUIT,
                            'NOM_PRODUIT' => $row->NOM_PRODUIT,
                            'NOM_CATEGORIE' => $row->NOM_CATEGORIE ?? 'Non Catégorisée',
                            'PRIX_VENTE' => $averagePrice,
                            'QUANTITE_STOCK' => $stockQuantity,
                            'STATUT_PRODUIT' => $statutProduit,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Gestion des erreurs améliorée pour le débogage
            error_log("Erreur dans ProductsController::indexAction : " . $e->getMessage() . " sur la ligne " . $e->getLine() . " dans " . $e->getFile());
            $this->flash->error("Une erreur s'est produite lors de la récupération des produits. Veuillez réessayer. " . $e->getMessage());
            // Vous pouvez commenter la ligne suivante en production, mais utile en développement
            // echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            $productsData = []; // Assurez-vous que $productsData est vide en cas d'erreur
        }

        // Passage des variables à la vue
        $this->view->setVars([
            'products' => $productsData,
            'canAddProducts' => $canAddProducts,
            'canViewProductsList' => $canViewProductsList,
            'canModifyProducts' => $canModifyProducts,
            'canDeleteProducts' => $canDeleteProducts,
            'currentUserRole' => $role
        ]);

        // Choisir le fichier de vue (par exemple, app/views/products/index.phtml)
        $this->view->pick('products/index');
    }

    public function createAction()
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission d'ajouter un produit.");
            return $this->response->redirect('products');
        }

        // Si la requête est POST (soumission du formulaire)
        if ($this->request->isPost()) {
            $product = new Produit();
            $stock = new Stock();

            // Récupérer les données du formulaire
            $nomProduit = $this->request->getPost('nom_produit', 'string');
            $idCategorie = $this->request->getPost('categorie', 'int');
            $quantiteStock = $this->request->getPost('quantite_stock', 'int');
            $prixVente = $this->request->getPost('prix_vente', 'float'); // Supposons un prix de vente initial

            // Valider les données (très simple ici, à améliorer en production)
            if (empty($nomProduit) || empty($idCategorie) || $quantiteStock < 0 || $prixVente <= 0) {
                $this->flash->error("Veuillez remplir tous les champs obligatoires et s'assurer que les valeurs sont valides.");
                return $this->response->redirect('products/create');
            }

            $this->db->begin(); // Démarre une transaction pour s'assurer que les deux insertions réussissent

            try {
                // Créer le produit
                $product->NOM_PRODUIT = $nomProduit;
                $product->ID_CATEGORIE = $idCategorie; // Assurez-vous que cette catégorie existe
                // D'autres attributs de Produit si vous en avez (e.g., DESCRIPTION_PRODUIT)

                if (!$product->save()) {
                    foreach ($product->getMessages() as $message) {
                        $this->flash->error("Erreur produit: " . (string) $message);
                    }
                    $this->db->rollback();
                    return $this->response->redirect('products/create');
                }

                // Créer le stock associé au produit
                $stock->ID_PRODUIT = $product->ID_PRODUIT;
                $stock->QUANTITE_STOCK = $quantiteStock;
                $stock->DATE_MISEJOUR = date('Y-m-d H:i:s'); // Date actuelle

                if (!$stock->save()) {
                    foreach ($stock->getMessages() as $message) {
                        $this->flash->error("Erreur stock: " . (string) $message);
                    }
                    $this->db->rollback();
                    return $this->response->redirect('products/create');
                }

                // Si vous voulez enregistrer un prix de vente initial pour un produit
                // Bien que 'PRIX_VENTE' ne soit pas dans le modèle Produit, il est dans Concerner.
                // Cela signifie qu'un prix de vente est associé à une vente spécifique.
                // Si vous voulez un prix de vente par défaut pour le produit, il faudrait ajouter
                // une colonne `PRIX_BASE_VENTE` dans la table PRODUITS, ou une logique similaire.
                // Pour l'instant, on laisse le calcul du prix moyen en lecture.

                $this->db->commit();
                $this->flash->success("Le produit '" . htmlspecialchars($product->NOM_PRODUIT) . "' a été ajouté avec succès.");
                return $this->response->redirect('products');
            } catch (\Exception $e) {
                $this->db->rollback();
                error_log("Erreur lors de l'ajout d'un produit : " . $e->getMessage());
                $this->flash->error("Une erreur inattendue est survenue lors de l'ajout du produit : " . $e->getMessage());
                return $this->response->redirect('products/create');
            }
        }

        // Récupérer les catégories pour le formulaire d'ajout
        $categories = Categorie::find();
        $this->view->setVar('categories', $categories);

        $this->view->pick('products/create');
    }

    public function editAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission de modifier un produit.");
            return $this->response->redirect('products');
        }

        $product = Produit::findFirstByID_PRODUIT($id);

        if (!$product) {
            $this->flash->error("Le produit n'existe pas.");
            return $this->response->redirect('products');
        }

        // Tenter de récupérer le stock associé
        $stock = Stock::findFirst([
            'conditions' => 'ID_PRODUIT = :productId:',
            'bind' => ['productId' => $product->ID_PRODUIT]
        ]);
        // Note: Le prix de vente affiché sera le prix moyen comme dans indexAction.
        // Si vous avez un champ PRIX_BASE_VENTE dans PRODUITS, utilisez-le ici.

        // Si la requête est POST (soumission du formulaire de modification)
        if ($this->request->isPost()) {
            $this->db->begin();

            try {
                $nomProduit = $this->request->getPost('nom_produit', 'string');
                $idCategorie = $this->request->getPost('categorie', 'int');
                $quantiteStock = $this->request->getPost('quantite_stock', 'int');
                // Pas de modification directe du prix de vente via ce formulaire si ce n'est pas un champ du modèle Produit

                // Valider les données
                if (empty($nomProduit) || empty($idCategorie) || $quantiteStock < 0) {
                    $this->flash->error("Veuillez remplir tous les champs obligatoires et s'assurer que les valeurs sont valides.");
                    $this->db->rollback();
                    return $this->response->redirect('products/edit/' . $id);
                }

                // Mettre à jour le produit
                $product->NOM_PRODUIT = $nomProduit;
                $product->ID_CATEGORIE = $idCategorie;
                if (!$product->save()) {
                    foreach ($product->getMessages() as $message) {
                        $this->flash->error("Erreur produit: " . (string) $message);
                    }
                    $this->db->rollback();
                    return $this->response->redirect('products/edit/' . $id);
                }

                // Mettre à jour le stock (ou le créer s'il n'existe pas)
                if (!$stock) {
                    $stock = new Stock();
                    $stock->ID_PRODUIT = $product->ID_PRODUIT;
                }
                $stock->QUANTITE_STOCK = $quantiteStock;
                $stock->DATE_MISEJOUR = date('Y-m-d H:i:s'); // Mise à jour de la date

                if (!$stock->save()) {
                    foreach ($stock->getMessages() as $message) {
                        $this->flash->error("Erreur stock: " . (string) $message);
                    }
                    $this->db->rollback();
                    return $this->response->redirect('products/edit/' . $id);
                }

                $this->db->commit();
                $this->flash->success("Le produit '" . htmlspecialchars($product->NOM_PRODUIT) . "' a été mis à jour avec succès.");
                return $this->response->redirect('products');
            } catch (\Exception $e) {
                $this->db->rollback();
                error_log("Erreur lors de la modification d'un produit : " . $e->getMessage());
                $this->flash->error("Une erreur inattendue est survenue lors de la modification du produit : " . $e->getMessage());
                return $this->response->redirect('products/edit/' . $id);
            }
        }

        // Pour l'affichage initial du formulaire
        $categories = Categorie::find();
        $this->view->setVars([
            'product' => $product,
            'stock' => $stock,
            'categories' => $categories
        ]);

        $this->view->pick('products/edit');
    }

    public function deleteAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role !== 'admin' && $role !== 'magasinier') {
            $this->flash->error("Vous n'avez pas la permission de supprimer un produit.");
            return $this->response->redirect('products');
        }

        $product = Produit::findFirstByID_PRODUIT($id);

        if (!$product) {
            $this->flash->error("Le produit n'existe pas.");
            return $this->response->redirect('products');
        }

        $this->db->begin(); // Démarre une transaction pour la suppression

        try {
            // Supprimer d'abord les enregistrements dépendants si nécessaire
            // Par exemple, les entrées dans la table STOCKS, CONCERNER, APPROVISIONNEMENTS
            // Vous devez définir les `onDelete` cascades dans vos modèles ou les supprimer manuellement.
            // Pour cet exemple, je vais juste tenter de supprimer le stock associé si la relation n'est pas cascade.
            $stock = Stock::findFirst([
                'conditions' => 'ID_PRODUIT = :productId:',
                'bind' => ['productId' => $product->ID_PRODUIT]
            ]);
            if ($stock) {
                if (!$stock->delete()) {
                    foreach ($stock->getMessages() as $message) {
                        $this->flash->error("Erreur lors de la suppression du stock lié: " . (string) $message);
                    }
                    $this->db->rollback();
                    return $this->response->redirect('products');
                }
            }

            // Si des entrées dans 'Concerner' doivent être supprimées avant le produit:
            // $concerns = Concerner::findByIDProduit($product->ID_PRODUIT);
            // if ($concerns) {
            //     foreach ($concerns as $concern) {
            //         if (!$concern->delete()) {
            //             // Gérer l'erreur
            //         }
            //     }
            // }

            if ($product->delete() === false) {
                foreach ($product->getMessages() as $message) {
                    $this->flash->error("Erreur produit: " . (string) $message);
                }
                $this->db->rollback();
            } else {
                $this->db->commit();
                $this->flash->success("Le produit a été supprimé avec succès.");
            }
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Erreur lors de la suppression d'un produit : " . $e->getMessage());
            $this->flash->error("Une erreur inattendue est survenue lors de la suppression du produit : " . $e->getMessage());
        }
        return $this->response->redirect('products');
    }

    public function viewAction($id)
    {
        $role = $this->session->get('auth')['role'] ?? 'guest';
        if ($role === 'guest') { // Ou si la permission spécifique n'est pas accordée
            $this->flash->error("Vous n'avez pas la permission de voir les détails d'un produit.");
            return $this->response->redirect('products');
        }

        $product = $this->modelsManager->createQuery(
            "SELECT p.ID_PRODUIT, p.NOM_PRODUIT, c.NOM_CATEGORIE, s.QUANTITE_STOCK
             FROM GestionStockVente\\Models\\Produit p
             LEFT JOIN GestionStockVente\\Models\\Categorie c ON p.ID_CATEGORIE = c.ID_CATEGORIE
             LEFT JOIN GestionStockVente\\Models\\Stock s ON p.ID_PRODUIT = s.ID_PRODUIT
             WHERE p.ID_PRODUIT = :productId:"
        )->execute(['productId' => $id])->getFirst();

        if (!$product) {
            $this->flash->error("Le produit n'existe pas.");
            return $this->response->redirect('products');
        }

        // Calcul du prix de vente moyen pour ce produit
        $averagePriceResult = $this->modelsManager->createQuery(
            "SELECT AVG(c.PRIX_UNITAIRE_VENDUE) as average_price
             FROM GestionStockVente\\Models\\Concerner c
             WHERE c.ID_PRODUIT = :productId:"
        )->execute(['productId' => $id]);

        $averagePrice = 'N/A';
        if ($averagePriceResult instanceof ResultsetInterface && $averagePriceResult->count() > 0) {
            $priceRow = $averagePriceResult->getFirst();
            if ($priceRow && $priceRow->average_price !== null) {
                $averagePrice = number_format((float) $priceRow->average_price, 2, ',', ' ') . ' Fbu';
            }
        }

        // Détermination du statut basé sur la quantité de stock
        $stockQuantity = $product->QUANTITE_STOCK ?? 0;
        $statutProduit = 'Disponible';
        if ($stockQuantity <= 5 && $stockQuantity > 0) {
            $statutProduit = 'Faible Stock';
        } elseif ($stockQuantity === 0) {
            $statutProduit = 'Rupture de Stock';
        }

        $productDetails = [
            'ID_PRODUIT' => $product->ID_PRODUIT,
            'NOM_PRODUIT' => $product->NOM_PRODUIT,
            'NOM_CATEGORIE' => $product->NOM_CATEGORIE ?? 'Non Catégorisée',
            'PRIX_VENTE' => $averagePrice,
            'QUANTITE_STOCK' => $stockQuantity,
            'STATUT_PRODUIT' => $statutProduit,
        ];

        $this->view->setVar('productDetails', $productDetails);
        $this->view->setVar('canModifyProducts', ($role === 'admin' || $role === 'magasinier'));

        $this->view->pick('products/view');
    }
}
