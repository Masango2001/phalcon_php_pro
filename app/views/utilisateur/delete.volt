{% extends "main.volt" %}

{% block content %}
    <h2>Suppression d'utilisateur</h2>
    <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
    {% if session.get('role') == 'admin' %}
        <form method="post" action="{{ url('utilisateur/delete/' ~ utilisateur.id_utilisateur) }}">
            <input type="hidden" name="id" value="{{ utilisateur.id_utilisateur }}">
            <button type="submit" class="btn btn-danger">Supprimer</button>
            <a href="{{ url('utilisateur/index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    {% else %}
        <div class="alert alert-danger">
            Seul un administrateur peut supprimer un utilisateur.
        </div>
        <a href="{{ url('utilisateur/index') }}" class="btn btn-secondary">Retour</a>
    {% endif %}
{% endblock %}</form>