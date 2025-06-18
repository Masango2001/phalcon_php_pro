<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Approvisionnement extends Model
{
    public $ID_APPROVISIONNEMENT;
    public $ID_PRODUIT;
    public $ID_FOURNISSEUR;
    public $QUANTITE_APPROVIONNEMENT;
    public $PRIX_UNITAIRE_ACHAT;
    public $DATE_APPROVISIONNEMENT;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("APPROVISIONNEMENTS");

        $this->belongsTo('ID_FOURNISSEUR', 'GestionStockVente\Models\Fournisseur', 'ID_FOURNISSEUR', ['alias' => 'Fournisseur']);
        $this->belongsTo('ID_PRODUIT', 'GestionStockVente\Models\Produit', 'ID_PRODUIT', ['alias' => 'Produit']);
    }
}
