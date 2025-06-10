{% extends 'layout/main.volt' %}

{% block content %}
<form method="post" action="{{ url('client/create') }}">
    <h2>Ajouter un client</h2>
    <div>
        <label for="nom_client">Nom :</label>
        <input type="text" id="nom_client" name="nom_client" required>
    </div>
    <div>
        <label for="prenom_client">Prénom :</label>
        <input type="text" id="prenom_client" name="prenom_client" required>
    </div>
    <div>
        <label for="adresse_client">Adresse :</label>
        <input type="text" id="adresse_client" name="adresse_client" required>
    </div>
    <div>
        <label for="telephone_client">Téléphone :</label>
        <input type="text" id="telephone_client" name="telephone_client" required>
    </div>
    <div>
        <button type="submit">Ajouter</button>
    </div>
</form>
{% endblock %}