<?php

class Magasinier extends Utilisateur{
    public function initialize()
    {
        parent::initialize();
        $this->role = 'magasinier';
    }

    // Gestion des produits
    public function ajouterProduit($productData)
    {
        $produit = new Produit();
        $produit->nom_produit = $productData['nom_produit'];
        
        if ($produit->save()) {
            // Créer un stock initial
            $stock = new Stock();
            $stock->id_produit = $produit->id_produit;
            $stock->quantite_stock = 0;
            $stock->dateMiseAjour = date('Y-m-d H:i:s');
            $stock->save();
            
            return $produit;
        }
        return false;
    }

    public function modifierProduit($id, $productData)
    {
        $produit = Produit::findFirst($id);
        if (!$produit) {
            return false;
        }

        $produit->nom_produit = $productData['nom_produit'];
        return $produit->save();
    }

    public function supprimerProduit($id)
    {
        $produit = Produit::findFirst($id);
        if ($produit) {
            return $produit->delete();
        }
        return false;
    }

    public function afficherProduits()
    {
        return Produit::find(['order' => 'nom_produit ASC']);
    }

    // Gestion des approvisionnements
    public function ajouterApprovisionnement($approData)
    {
        $appro = new Approvisionnement();
        $appro->id_produit = $approData['id_produit'];
        $appro->quantite_approvisionnement = $approData['quantite'];
        $appro->prix_unitaire_achat = $approData['prix_achat'];
        $appro->date_approvisionnement = date('Y-m-d H:i:s');

        if ($appro->save()) {
            // Mettre à jour le stock
            $this->mettreAJourStock($appro->id_produit, $appro->quantite_approvisionnement);
            return $appro;
        }
        return false;
    }

    private function mettreAJourStock($id_produit, $quantite)
    {
        $stock = Stock::findFirst([
            'conditions' => 'id_produit = :produit:',
            'bind' => ['produit' => $id_produit]
        ]);

        if ($stock) {
            $stock->quantite_stock += $quantite;
            $stock->dateMiseAjour = date('Y-m-d H:i:s');
            return $stock->save();
        }
        return false;
    }

    public function afficherStock()
    { 
        return $this->modelsManager->createQuery(
            'SELECT s.*, p.nom_produit 
             FROM Stock s 
             JOIN Produit p ON s.id_produit = p.id_produit 
             ORDER BY p.nom_produit'
        )->execute();
    }

    public function calculTotalAchat()
    {
        return Approvisionnement::sum([
            'column' => 'quantite_approvisionnement * prix_unitaire_achat'
        ]);
    }

    public function afficherApprovisionnements()
    {
        return Approvisionnement::find([
            'order' => 'date_approvisionnement DESC'
        ]);
    }
}