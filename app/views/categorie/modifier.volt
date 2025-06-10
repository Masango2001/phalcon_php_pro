{% extends "layout/main.volt" %}

{% block content %}
<h2>Modifier la catégorie</h2>

<form action="{{ url('categorie/update/' ~ categorie.id_categorie) }}" method="post">
    <div>
        <label for="nom_categorie">Nom de la catégorie :</label>
        <input type="text" id="nom_categorie" name="nom_categorie" value="{{ categorie.nom_categorie }}" required>
    </div>
    <div>
        <button type="submit">Enregistrer</button>
        <a href="{{ url('categorie') }}">Annuler</a>
    </div>
</form>
{% endblock %}