use Phalcon\Mvc\Model;

<?php


class Client extends Model
{
    public $id_client;
    public $nom_client;
    public $prenom_client;
    public $adresse_client;
    public $telephone_client;

    public function initialize()
    {
        $this->setSource('clients');
        $this->hasMany(
            'id_client',
            Vente::class,
            'id_client',
            [
                'alias' => 'ventes'
            ]
        );
    }
}