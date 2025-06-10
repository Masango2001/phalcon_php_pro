{% extends "layout/main.volt" %}

{% block content %}
<h2>Supprimer la catégorie</h2>

{% if categorie is defined %}
    <p>Voulez-vous vraiment supprimer la catégorie : <strong>{{ categorie.nom_categorie }}</strong> ?</p>
    <form method="post" action="{{ url('categorie/delete/' ~ categorie.id_categorie) }}">
        <button type="submit" class="btn btn-danger">Supprimer</button>
        <a href="{{ url('categorie/index') }}" class="btn btn-secondary">Annuler</a>
    </form>
{% else %}
    <div class="alert alert-danger">
        Catégorie non trouvée.
    </div>
    <a href="{{ url('categorie/index') }}" class="btn btn-secondary">Retour</a>
{% endif %}
{% endblock %}