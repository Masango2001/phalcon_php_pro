{% extends "layout/main.volt" %}

{% block content %}
<h2>Ajouter un produit</h2>

{{ flashSession.output() }}

<form action="{{ url('produit/create') }}" method="post">
    <div>
        <label for="id_categorie">Catégorie :</label>
        <input type="number" name="id_categorie" id="id_categorie" required>
    </div>
    <div>
        <label for="nom_produit">Nom du produit :</label>
        <input type="text" name="nom_produit" id="nom_produit" required>
    </div>
    <div>
        <button type="submit">Ajouter</button>
    </div>
</form>
{% endblock %}