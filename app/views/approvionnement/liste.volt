{% extends 'layout/main.volt' %}

{% block content %}
<h1>Liste des approvisionnements</h1>

{% if flashSession.has('success') %}
    <div class="alert alert-success">{{ flashSession.get('success') }}</div>
{% endif %}
{% if flashSession.has('error') %}
    <div class="alert alert-danger">{{ flashSession.get('error') }}</div>
{% endif %}

<a href="{{ url('approvisionnement/new') }}" class="btn btn-primary">Nouvel approvisionnement</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fournisseur</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    {% for appro in approvisionnements %}
        <tr>
            <td>{{ appro.id_approvisionnement }}</td>
            <td>{{ appro.fournisseur.nom }}</td>
            <td>{{ appro.produit.nom }}</td>
            <td>{{ appro.quantite_approvisionnement }}</td>
            <td>{{ appro.prix_unitaire_achat }}</td>
            <td>{{ appro.date_approvisionnement }}</td>
            <td>
                <a href="{{ url('approvisionnement/edit/' ~ appro.id_approvisionnement) }}" class="btn btn-sm btn-warning">Modifier</a>
                <a href="{{ url('approvisionnement/delete/' ~ appro.id_approvisionnement) }}" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet approvisionnement ?');">Supprimer</a>
            </td>
        </tr>
    {% else %}
        <tr>
            <td colspan="7">Aucun approvisionnement trouvé.</td>
        </tr>
    {% endfor %}
    </tbody>
</table>
{% endblock %}