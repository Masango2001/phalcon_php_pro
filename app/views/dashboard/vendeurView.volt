{% extends 'layouts/main.volt' %}

{% block content %}
<div class="container">
    <h1 class="mb-4">Bienvenue sur le tableau de bord du vendeur</h1>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Ventes</h5>
                    <p class="card-text display-6">{{ ventes|length }}</p>
                    <a href="/vendeur/ventes" class="btn btn-light btn-sm">Voir les ventes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Clients</h5>
                    <p class="card-text display-6">{{ clients|length }}</p>
                    <a href="/vendeur/clients" class="btn btn-light btn-sm">Voir les clients</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Produits Concernés</h5>
                    <p class="card-text display-6">{{ concerner|length }}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Liste des ventes récentes -->
    <div class="card mt-4">
        <div class="card-header">
            Ventes récentes
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Produit</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for vente in ventes|slice(0,5) %}
                        <tr>
                            <td>{{ vente.id_vente }}</td>
                            <td>{{ vente.date_vente }}</td>
                            <td>
                                {% set client = clients|filter(c => c.id_client == vente.id_client)|first %}
                                {{ client ? client.nom_client ~ ' ' ~ client.prenom_client : 'N/A' }}
                            </td>
                            <td>{{ vente.id_produit }}</td>
                        </tr>
                        {% else %}
                        <tr>
                            <td colspan="4" class="text-center">Aucune vente récente</td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{% endblock %}</div>