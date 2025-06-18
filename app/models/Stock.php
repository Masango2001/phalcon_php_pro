<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Stock extends Model
{
    public $ID_STOCK;
    public $ID_PRODUIT;
    public $QUANTITE_STOCK;
    public $DATE_MISEJOUR;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("STOCKS");

        $this->belongsTo('ID_PRODUIT', 'GestionStockVente\Models\Produit', 'ID_PRODUIT', ['alias' => 'Produit']);
    }
}
