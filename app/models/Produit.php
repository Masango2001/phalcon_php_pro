<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Produit extends Model
{
    public $ID_PRODUIT;
    public $ID_CATEGORIE;
    public $NOM_PRODUIT;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("PRODUITS");

        $this->hasMany('ID_PRODUIT', 'GestionStockVente\Models\Approvisionnement', 'ID_PRODUIT', ['alias' => 'Approvisionnements']);
        $this->hasMany('ID_PRODUIT', 'GestionStockVente\Models\Concerner', 'ID_PRODUIT', ['alias' => 'Concerner']);
        $this->hasMany('ID_PRODUIT', 'GestionStockVente\Models\Stock', 'ID_PRODUIT', ['alias' => 'Stocks']);
        $this->belongsTo('ID_CATEGORIE', 'GestionStockVente\Models\Categorie', 'ID_CATEGORIE', ['alias' => 'Categorie']);
    }
}
