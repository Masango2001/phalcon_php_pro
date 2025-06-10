{% extends "layout/main.volt" %}

{% block content %}
<h2>Modifier le produit</h2>

<form action="{{ url('produit/update/' ~ produit.id_produit) }}" method="post">
    <div>
        <label for="id_categorie">Catégorie :</label>
        <input type="number" id="id_categorie" name="id_categorie" value="{{ produit.id_categorie }}" required>
    </div>
    <div>
        <label for="nom_produit">Nom du produit :</label>
        <input type="text" id="nom_produit" name="nom_produit" value="{{ produit.nom_produit }}" required>
    </div>
    <div>
        <button type="submit">Enregistrer</button>
        <a href="{{ url('produit/index') }}">Annuler</a>
    </div>
</form>
{% endblock %}