{% extends 'layouts/main.volt' %}

{% block title %}Tableau de bord Magasinier{% endblock %}

{% block content %}
<style>
    .dashboard-card { margin-bottom: 20px; }
    .table th, .table td { vertical-align: middle; }
</style>

<h1 class="mb-4">Tableau de bord du Magasinier</h1>

<div class="row mb-4">
    <div class="col-md-3 dashboard-card">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Produits</h5>
                <p class="card-text"><b>{{ nbProduits }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 dashboard-card">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Catégories</h5>
                <p class="card-text"><b>{{ nbCategories }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 dashboard-card">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Stocks</h5>
                <p class="card-text"><b>{{ nbStocks }}</b></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 dashboard-card">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Approvisionnements</h5>
                <p class="card-text"><b>{{ nbApprovisionnements }}</b></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Derniers produits -->
    <div class="col-md-6">
        <h4>Derniers produits ajoutés</h4>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                </tr>
            </thead>
            <tbody>
            {% for produit in derniersProduits %}
                <tr>
                    <td>{{ produit.id_produit }}</td>
                    <td>{{ produit.nom_produit }}</td>
                    <td>{{ produit.categorie.nom_categorie }}</td>
                </tr>
            {% else %}
                <tr><td colspan="3">Aucun produit récent.</td></tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
    <!-- Stocks faibles -->
    <div class="col-md-6">
        <h4>Stocks faibles (&lt; 10)</h4>
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
                    <td class="text-danger"><b>{{ stock.quantite_stock }}</b></td>
                </tr>
            {% else %}
                <tr><td colspan="2">Aucun stock faible.</td></tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-4">
    <!-- Derniers approvisionnements -->
    <div class="col-md-12">
        <h4>Derniers approvisionnements</h4>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            {% for appro in derniersApprovisionnements %}
                <tr>
                    <td>{{ appro.id_approvisionnement }}</td>
                    <td>{{ appro.produit.nom_produit }}</td>
                    <td>{{ appro.quantite }}</td>
                    <td>{{ appro.date_approvisionnement|date("d/m/Y") }}</td>
                </tr>
            {% else %}
                <tr><td colspan="4">Aucun approvisionnement récent.</td></tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}</div>