{% extends 'layout/main.volt' %}

{% block content %}
    {% if approvisionnement is defined %}
        <h2>Suppression d'un approvisionnement</h2>
        <p>Êtes-vous sûr de vouloir supprimer l'approvisionnement suivant&nbsp;?</p>
        <ul>
            <li><strong>ID :</strong> {{ approvisionnement.id_approvisionnement }}</li>
            <li><strong>Fournisseur :</strong> {{ approvisionnement.fournisseur.nom }}</li>
            <li><strong>Produit :</strong> {{ approvisionnement.produit.nom }}</li>
            <li><strong>Quantité :</strong> {{ approvisionnement.quantite_approvisionnement }}</li>
            <li><strong>Prix unitaire :</strong> {{ approvisionnement.prix_unitaire_achat }}</li>
            <li><strong>Date :</strong> {{ approvisionnement.date_approvisionnement }}</li>
        </ul>
        <form method="post" action="{{ url('approvisionnement/delete/' ~ approvisionnement.id_approvisionnement) }}">
            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
            <a href="{{ url('approvisionnement') }}" class="btn btn-secondary">Annuler</a>
        </form>
    {% else %}
        <div class="alert alert-danger">
            Approvisionnement introuvable.
        </div>
        <a href="{{ url('approvisionnement') }}" class="btn btn-primary">Retour à la liste</a>
    {% endif %}
{% endblock %}