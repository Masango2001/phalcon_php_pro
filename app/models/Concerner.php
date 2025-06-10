use Phalcon\Mvc\Model;

<?php


class Concerner extends Model
{
    public $id_vente;
    public $id_produit;
    public $quantite_vendue;
    public $prix_unitaire_vendue;

    public function initialize()
    {
        $this->setSource('concerner');

        $this->belongsTo('id_vente', Vente::class, 'id_vente', [
            'alias' => 'Vente'
        ]);
        $this->belongsTo('id_produit', Produit::class, 'id_produit', [
            'alias' => 'Produit'
        ]);
    }
}