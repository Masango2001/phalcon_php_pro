use Phalcon\Mvc\Model;

<?php

class Vente extends Model
{
    public $id_vente;
    public $id_client;
    public $id_utilisateur;
    public $date_vente;
    public $id_produit;

    public function initialize()
    {
        $this->setSource('ventes');

        $this->belongsTo(
            'id_produit',
            Produit::class,
            'id_produit'
        );

        $this->belongsTo(
            'id_client',
            Client::class,
            'id_client'
        );

        $this->belongsTo(
            'id_utilisateur',
            Utilisateur::class,
            'id_utilisateur'
        );
    }

    public function getSource()
    {
        return 'ventes';
    }
}