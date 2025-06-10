use Phalcon\Mvc\Model;

<?php


class Stock extends Model
{
    public $id_stock;
    public $id_produit;
    public $quantite_stock;
    public $date_Misejour;

    public function initialize()
    {
        $this->setSource('stocks');

        $this->belongsTo(
            'id_produit',
            Produit::class,
            'id_produit',
            [
                'alias' => 'produit'
            ]
        );
    }

    public function getSource()
    {
        return 'stocks';
    }
}