<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Categorie extends Model
{
    public $ID_CATEGORIE;
    public $NOM_CATEGORIE;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("CATEGORIES");

        $this->hasMany('ID_CATEGORIE', 'GestionStockVente\Models\Produit', 'ID_CATEGORIE', ['alias' => 'Produits']);
    }
}
