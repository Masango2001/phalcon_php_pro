{% extends 'layout/main.volt' %}

{% block content %}
<h2>Supprimer le produit</h2>

{% if produit is defined %}
    <p>Voulez-vous vraiment supprimer le produit <strong>{{ produit.nom_produit }}</strong> ?</p>
    <form method="post" action="{{ url('produit/delete/' ~ produit.id_produit) }}">
        <input type="hidden" name="id_produit" value="{{ produit.id_produit }}">
        <button type="submit" class="btn btn-danger">Supprimer</button>
        <a href="{{ url('produit/index') }}" class="btn btn-secondary">Annuler</a>
    </form>
{% else %}
    <p>Produit introuvable.</p>
    <a href="{{ url('produit/index') }}" class="btn btn-secondary">Retour</a>
{% endif %}
{% endblock %}</form>