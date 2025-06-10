{% extends 'layouts/main.volt' %}

{% block content %}
<div class="container mt-4">
    <h2>Liste des Produits</h2>
    {% if flashSession.has('success') %}
        <div class="alert alert-success">{{ flashSession.get('success') }}</div>
    {% endif %}
    {% if flashSession.has('error') %}
        <div class="alert alert-danger">{{ flashSession.get('error') }}</div>
    {% endif %}

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Catégorie</th>
                <th>Nom du Produit</th>
            </tr>
        </thead>
        <tbody>
        {% for produit in produits %}
            <tr>
                <td>{{ produit.id_produit }}</td>
                <td>{{ produit.id_categorie }}</td>
                <td>{{ produit.nom_produit }}</td>
            </tr>
        {% else %}
            <tr>
                <td colspan="3" class="text-center">Aucun produit trouvé.</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
</div>
{% endblock %}