{% extends 'layouts/main.volt' %}

{% block title %}Tableau de bord Administrateur{% endblock %}

{% block content %}
<div class="container mt-4">
    <h1 class="mb-4">Tableau de bord Administrateur</h1>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col stat-card">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Produits</h5>
                    <p class="card-text"><b>{{ nbProduits }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Catégories</h5>
                    <p class="card-text"><b>{{ nbCategories }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Clients</h5>
                    <p class="card-text"><b>{{ nbClients }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Stocks</h5>
                    <p class="card-text"><b>{{ nbStocks }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h5 class="card-title">Approvisionnements</h5>
                    <p class="card-text"><b>{{ nbApprovisionnements }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Ventes</h5>
                    <p class="card-text"><b>{{ nbVentes }}</b></p>
                </div>
            </div>
        </div>
        <div class="col stat-card">
            <div class="card text-bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text"><b>{{ nbUtilisateurs }}</b></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liens de navigation rapide -->
    <div class="quick-links mb-4">
        <h4>Navigation rapide</h4>
        {% for link in adminLinks %}
            <a href="{{ url(link.url) }}" class="btn btn-outline-primary btn-sm">{{ link.label }}</a>
        {% endfor %}
    </div>

    <div class="row">
        <!-- Derniers produits -->
        <div class="col-md-6 mb-4">
            <h5>Derniers produits ajoutés</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                {% for produit in derniersProduits %}
                    <tr>
                        <td>{{ produit.nom_produit }}</td>
                        <td>{{ produit.categorie.nom_categorie }}</td>
                        <td>{{ produit.prix_unitaire }} €</td>
                    </tr>
                {% else %}
                    <tr><td colspan="3">Aucun produit récent.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>

        <!-- Derniers clients -->
        <div class="col-md-6 mb-4">
            <h5>Derniers clients inscrits</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                    </tr>
                </thead>
                <tbody>
                {% for client in derniersClients %}
                    <tr>
                        <td>{{ client.nom_client }}</td>
                        <td>{{ client.email }}</td>
                        <td>{{ client.telephone }}</td>
                    </tr>
                {% else %}
                    <tr><td colspan="3">Aucun client récent.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <!-- Dernières ventes -->
        <div class="col-md-6 mb-4">
            <h5>Dernières ventes</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                {% for vente in derniersVentes %}
                    <tr>
                        <td>{{ vente.date_vente|date("d/m/Y") }}</td>
                        <td>{{ vente.client.nom_client }}</td>
                        <td>{{ vente.montant_total }} €</td>
                    </tr>
                {% else %}
                    <tr><td colspan="3">Aucune vente récente.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>

        <!-- Stocks faibles -->
        <div class="col-md-6 mb-4">
            <h5>Stocks faibles (&lt; 10)</h5>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                    </tr>
                </thead>
                <tbody>
                {% for stock in stocksFaibles %}
                    <tr>
                        <td>{{ stock.produit.nom_produit }}</td>
                        <td>{{ stock.quantite_stock }}</td>
                    </tr>
                {% else %}
                    <tr><td colspan="2">Aucun stock faible.</td></tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>
{% endblock %}