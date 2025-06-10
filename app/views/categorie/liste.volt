{% extends "layout/main.volt" %}

{% block content %}
<h1>Liste des catégories</h1>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom de la catégorie</th>
        </tr>
    </thead>
    <tbody>
        {% for categorie in categories %}
        <tr>
            <td>{{ categorie.id_categorie }}</td>
            <td>{{ categorie.nom_categorie }}</td>
        </tr>
        {% else %}
        <tr>
            <td colspan="2">Aucune catégorie trouvée.</td>
        </tr>
        {% endfor %}
    </tbody>
</table>
{% endblock %}