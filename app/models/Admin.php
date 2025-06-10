<?php

class Admin extends Utilisateur
{
    public function initialize()
    {
        parent::initialize();
        $this->role = 'admin';
    }

    // Méthodes spécifiques à l'Admin
    public function ajouterUtilisateur($userData)
    {
        $user = new Utilisateur();
        $user->username = $userData['username'];
        $user->email = $userData['email'];
        $user->password = $userData['password'];
        $user->role = $userData['role'];
        
        return $user->save();
    }

    public function modifierUtilisateur($id, $userData)
    {
        $user = Utilisateur::findFirst($id);
        if (!$user) {
            return false;
        }

        $user->username = $userData['username'];
        $user->email = $userData['email'];
        $user->role = $userData['role'];
        
        if (!empty($userData['password'])) {
            $user->password = $userData['password'];
        }

        return $user->save();
    }

    public function supprimerUtilisateur($id)
    {
        $user = Utilisateur::findFirst($id);
        if ($user && $user->id_utilisateur != $this->id_utilisateur) {
            return $user->delete();
        }
        return false;
    }

    public function afficherUtilisateurs()
    {
        return Utilisateur::find();
    }

    // Statistiques globales du système
    public function getStatistiquesGlobales()
    {
        return [
            'total_users' => Utilisateur::count(),
            'total_products' => Produit::count(),
            'total_stock' => Stock::sum(['column' => 'quantite_stock']),
            'total_ventes_today' => Vente::count([
                'conditions' => 'DATE(date_vente) = CURDATE()'
            ])
        ];
    }
}