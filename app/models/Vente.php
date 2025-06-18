<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Vente extends Model
{
    public $ID_VENTE;
    public $ID_UTILISATEUR;
    public $ID_CLIENT;
    public $DATE_VENTE;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("VENTES");

        $this->hasMany('ID_VENTE', 'GestionStockVente\Models\Concerner', 'ID_VENTE', ['alias' => 'Concerner']);
        $this->belongsTo('ID_CLIENT', 'GestionStockVente\Models\Client', 'ID_CLIENT', ['alias' => 'Client']);
        $this->belongsTo('ID_UTILISATEUR', 'GestionStockVente\Models\Utilisateur', 'ID_UTILISATEUR', ['alias' => 'Utilisateur']);
    }
}
