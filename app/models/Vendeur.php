<?php

class Vendeur extends Utilisateur
{
    public function initialize()
    {
        parent::initialize();
        $this->role = 'vendeur';
        
        $this->hasMany(
            'id_utilisateur',
            'Vente',
            'id_utilisateur'
        );
    }

    // Gestion des ventes
    public function effectuerVente($venteData)
    {
        // Vérifier le stock disponible
        if (!$this->verifierStock($venteData['id_produit'], $venteData['quantite'])) {
            return ['success' => false, 'message' => 'Stock insuffisant'];
        }

        $vente = new Vente();
        $vente->id_produit = $venteData['id_produit'];
        $vente->id_client = $venteData['id_client'] ?? null;
        $vente->id_utilisateur = $this->id_utilisateur;
        $vente->quantite_vendue = $venteData['quantite'];
        $vente->prix_unitaire_vendue = $venteData['prix_vente'];
        $vente->date_vente = date('Y-m-d H:i:s');

        if ($vente->save()) {
            // Décrémenter le stock
            $this->deduireStock($vente->id_produit, $vente->quantite_vendue);
            return ['success' => true, 'vente' => $vente];
        }

        return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement'];
    }

    private function verifierStock($id_produit, $quantite_demandee)
    {
        $stock = Stock::findFirst([
            'conditions' => 'id_produit = :produit:',
            'bind' => ['produit' => $id_produit]
        ]);

        return $stock && $stock->quantite_stock >= $quantite_demandee;
    }

    private function deduireStock($id_produit, $quantite)
    {
        $stock = Stock::findFirst([
            'conditions' => 'id_produit = :produit:',
            'bind' => ['produit' => $id_produit]
        ]);

        if ($stock) {
            $stock->quantite_stock -= $quantite;
            $stock->dateMiseAjour = date('Y-m-d H:i:s');
            return $stock->save();
        }
        return false;
    }

    public function enregistrerVente($venteData)
    {
        return $this->effectuerVente($venteData);
    }

    public function modifierVente($id, $venteData)
    {
        $vente = Vente::findFirst([
            'conditions' => 'id_vente = :id: AND id_utilisateur = :user:',
            'bind' => ['id' => $id, 'user' => $this->id_utilisateur]
        ]);

        if (!$vente) {
            return false;
        }

        // Restaurer l'ancien stock
        $this->restaurerStock($vente->id_produit, $vente->quantite_vendue);
        
        // Vérifier le nouveau stock
        if (!$this->verifierStock($venteData['id_produit'], $venteData['quantite'])) {
            // Remettre l'ancien stock
            $this->deduireStock($vente->id_produit, $vente->quantite_vendue);
            return false;
        }

        // Mettre à jour la vente
        $vente->id_produit = $venteData['id_produit'];
        $vente->quantite_vendue = $venteData['quantite'];
        $vente->prix_unitaire_vendue = $venteData['prix_vente'];

        if ($vente->save()) {
            $this->deduireStock($vente->id_produit, $vente->quantite_vendue);
            return $vente;
        }

        return false;
    }

    private function restaurerStock($id_produit, $quantite)
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

    public function afficherVentes()
    {
        return $this->modelsManager->createQuery(
            'SELECT v.*, p.nom_produit, c.nom_client 
             FROM Vente v 
             JOIN Produit p ON v.id_produit = p.id_produit 
             LEFT JOIN Client c ON v.id_client = c.id_client 
             WHERE v.id_utilisateur = :user:
             ORDER BY v.date_vente DESC'
        )->execute(['user' => $this->id_utilisateur]);
    }

    public function calculTotalVendue()
    {
        return Vente::sum([
            'column' => 'quantite_vendue * prix_unitaire_vendue',
            'conditions' => 'id_utilisateur = :user:',
            'bind' => ['user' => $this->id_utilisateur]
        ]);
    }

    // Gestion des clients
    public function ajouterClient($clientData)
    {
        $client = new Client();
        $client->nom_client = $clientData['nom_client'];
        $client->prenom_client = $clientData['prenom_client'];
        $client->adresse_client = $clientData['adresse_client'];
        $client->telephone_client = $clientData['telephone_client'];

        return $client->save() ? $client : false;
    }

    public function afficherClients()
    {
        return Client::find(['order' => 'nom_client ASC']);
    }

    public function getProduitsDisponibles()
    {
        return $this->modelsManager->createQuery(
            'SELECT p.*, s.quantite_stock 
             FROM Produit p 
             JOIN Stock s ON p.id_produit = s.id_produit 
             WHERE s.quantite_stock > 0 
             ORDER BY p.nom_produit'
        )->execute();
    }
}