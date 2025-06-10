{% extends "layouts/main.volt" %}

{% block content %}
<h2>Ajouter un approvisionnement</h2>

{{ flashSession.output() }}

<form action="{{ url('approvisionnement/create') }}" method="post">
    <div>
        <label for="id_fournisseur">Fournisseur :</label>
        <select name="id_fournisseur" id="id_fournisseur" required>
            <option value="">-- Sélectionner --</option>
            {% for fournisseur in fournisseurs %}
                <option value="{{ fournisseur.id_fournisseur }}">{{ fournisseur.nom_fournisseur }}</option>
            {% endfor %}
        </select>
    </div>

    <div>
        <label for="id_produit">Produit :</label>
        <select name="id_produit" id="id_produit" required>
            <option value="">-- Sélectionner --</option>
            {% for produit in produits %}
                <option value="{{ produit.id_produit }}">{{ produit.nom_produit }}</option>
            {% endfor %}
        </select>
    </div>

    <div>
        <label for="quantite_approvisionnement">Quantité :</label>
        <input type="number" name="quantite_approvisionnement" id="quantite_approvisionnement" min="1" required>
    </div>

    <div>
        <label for="prix_unitaire_achat">Prix unitaire d'achat :</label>
        <input type="number" step="0.01" name="prix_unitaire_achat" id="prix_unitaire_achat" min="0" required>
    </div>

    <div>
        <label for="date_approvisionnement">Date d'approvisionnement :</label>
        <input type="date" name="date_approvisionnement" id="date_approvisionnement" required>
    </div>

    <div>
        <button type="submit">Ajouter</button>
        <a href="{{ url('approvisionnement') }}">Annuler</a>
    </div>
</form>
{% endblock %}