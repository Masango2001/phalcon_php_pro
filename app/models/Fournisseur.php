use Phalcon\Mvc\Model;

<?php


class Fournisseur extends Model
{
    public $id_fournisseur;
    public $nom_complet_fournisseur;
    public $adresse_fournisseur;
    public $email_fournisseur;
    public $telephone_fournisseur;

    public function initialize()
    {
        $this->setSource('fournisseurs');
        $this->hasMany(
            'id_fournisseur',
            Approvisionnement::class,
            'id_fournisseur'
        );
    }
}