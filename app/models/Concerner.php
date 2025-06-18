<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Concerner extends Model
{
    public $ID_PRODUIT;
    public $ID_VENTE;
    public $QUANTITE_VENDUE;
    public $PRIX_UNITAIRE_VENDUE;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("CONCERNER");

        $this->belongsTo('ID_PRODUIT', 'GestionStockVente\Models\Produit', 'ID_PRODUIT', ['alias' => 'Produit']);
        $this->belongsTo('ID_VENTE', 'GestionStockVente\Models\Vente', 'ID_VENTE', ['alias' => 'Vente']);
    }
}
