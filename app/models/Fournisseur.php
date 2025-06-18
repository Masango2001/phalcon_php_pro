<?php

namespace GestionStockVente\Models;

use Phalcon\Mvc\Model;

class Fournisseur extends Model
{
    public $ID_FOURNISSEUR;
    public $NOM_COMPLET_FOURNISSEUR;
    public $ADRESSE_FOURNISSEUR;
    public $EMAIL_FOURNISSEUR;
    public $TELEPHONE_FOURNISSEUR;

    public function initialize()
    {
        $this->setSchema("stocketvente");
        $this->setSource("FOURNISSEURS");

        $this->hasMany('ID_FOURNISSEUR', 'GestionStockVente\Models\Approvisionnement', 'ID_FOURNISSEUR', ['alias' => 'Approvisionnements']);
    }
}
