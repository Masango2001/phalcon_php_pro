use Phalcon\Mvc\Model;

<?php


class Categorie extends Model
{
    public $id_categorie;
    public $nom_categorie;

    public function initialize()
    {
        $this->setSource('categories');
        $this->hasMany(
            'id_categorie',
            Produit::class,
            'id_categorie',
            [
                'alias' => 'Produits'
            ]
        );
    }
}