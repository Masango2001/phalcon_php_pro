{% extends 'layout/main.volt' %}

{% block content %}
<h2>Modifier un approvisionnement</h2>

<form action="{{ url('approvisionnement/update/' ~ approvisionnement.id_approvisionnement) }}" method="post">
    <div>
        <label for="id_fournisseur">Fournisseur :</label>
        <select name="id_fournisseur" id="id_fournisseur" required>
            {% for fournisseur in fournisseurs %}
                <option value="{{ fournisseur.id_fournisseur }}"
                    {% if fournisseur.id_fournisseur == approvisionnement.id_fournisseur %}selected{% endif %}>
                    {{ fournisseur.nom_fournisseur }}
                </option>
            {% endfor %}
        </select>
    </div>
    <div>
        <label for="id_produit">Produit :</label>
        <select name="id_produit" id="id_produit" required>
            {% for produit in produits %}
                <option value="{{ produit.id_produit }}"
                    {% if produit.id_produit == approvisionnement.id_produit %}selected{% endif %}>
                    {{ produit.nom_produit }}
                </option>
            {% endfor %}
        </select>
    </div>
    <div>
        <label for="quantite_approvisionnement">Quantité :</label>
        <input type="number" name="quantite_approvisionnement" id="quantite_approvisionnement" min="1" required value="{{ approvisionnement.quantite_approvisionnement }}">
    </div>
    <div>
        <label for="prix_unitaire_achat">Prix unitaire d'achat :</label>
        <input type="number" step="0.01" name="prix_unitaire_achat" id="prix_unitaire_achat" required value="{{ approvisionnement.prix_unitaire_achat }}">
    </div>
    <div>
        <label for="date_approvisionnement">Date :</label>
        <input type="date" name="date_approvisionnement" id="date_approvisionnement" required value="{{ approvisionnement.date_approvisionnement }}">
    </div>
    <div>
        <button type="submit">Enregistrer les modifications</button>
        <a href="{{ url('approvisionnement') }}">Annuler</a>
    </div>
</form>
{% endblock %}