{% extends "layout/main.volt" %}

{% block content %}
<h2>Ajouter un utilisateur</h2>

{% if session.get('auth')['role'] == 'admin' %}
<form action="{{ url('utilisateur/create') }}" method="post">
    <div>
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="role">Rôle</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="utilisateur">Utilisateur</option>
        </select>
    </div>
    <div>
        <button type="submit">Ajouter</button>
    </div>
</form>
{% else %}
    <p>Accès refusé. Seul un administrateur peut ajouter un utilisateur.</p>
{% endif %}
{% endblock %}