<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Client extends Model
{
    public $ID_CLIENT;
    public $NOM_CLIENT;
    public $PRENOM_CLIENT;
    public $ADRESSE_CLIENT;
    public $TELEPHONE_CLIENT;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("CLIENTS");

        $this->hasMany('ID_CLIENT', 'GestionStockVente\Models\Vente', 'ID_CLIENT', ['alias' => 'Ventes']);
    }
}
