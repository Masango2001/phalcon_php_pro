use Phalcon\Mvc\Model;

<?php

class Produit extends Model
{
    public $id_produit;
    public $id_categorie;
    public $nom_produit;

    public function initialize()
    {
        $this->setSource('produits');

        $this->hasOne(
            'id_produit',
            Stock::class,
            'id_produit'
        );

        $this->hasMany(
            'id_produit',
            Vente::class,
            'id_produit'
        );

        $this->hasMany(
            'id_produit',
            Approvisionnement::class,
            'id_produit'
        );
    }

    public function getSource()
    {
        return 'produits';
    }
}