<?php

// Model Approvisionnement
class Approvisionnement extends \Phalcon\Mvc\Model
{
    public $id_approvisionnement;
    public $id_fournisseur;
    public $id_produit;
    public $quantite_approvisionnement;
    public $prix_unitaire_achat; // Ajouté ici
    public $date_approvisionnement;

    public function initialize()
    {
        $this->belongsTo(
            'id_fournisseur',
            'Fournisseur',
            'id_fournisseur',
            [
                'alias' => 'Fournisseur'
            ]
        );
        $this->belongsTo(
            'id_produit',
            'Produit',
            'id_produit',
            [
                'alias' => 'Produit'
            ]
        );
    }
}