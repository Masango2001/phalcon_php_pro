{% extends 'layouts/main.volt' %}

{% block content %}
<div class="container mt-5">
    <h2>Ajouter une Catégorie</h2>
    {% if flashSession.has('error') %}
        <div class="alert alert-danger">{{ flashSession.get('error') }}</div>
    {% endif %}
    {% if flashSession.has('success') %}
        <div class="alert alert-success">{{ flashSession.get('success') }}</div>
    {% endif %}
    <form action="{{ url('categorie/create') }}" method="post">
        <div class="mb-3">
            <label for="nom_categorie" class="form-label">Nom de la catégorie</label>
            <input type="text" class="form-control" id="nom_categorie" name="nom_categorie" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="{{ url('categorie/index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
{% endblock %}</div>