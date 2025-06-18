<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Utilisateur extends Model
{
    public $ID_UTILISATEUR;
    public $USERNAME;
    public $EMAIL;
    public $PASSWORD;
    public $ROLE;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("UTILISATEURS");

        $this->hasMany('ID_UTILISATEUR', 'GestionStockVente\Models\Vente', 'ID_UTILISATEUR', ['alias' => 'Ventes']);
    }
}
