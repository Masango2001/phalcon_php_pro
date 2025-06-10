{% extends "layout/main.volt" %}

{% block content %}
<div class="container">
    <h2>Modifier l'utilisateur</h2>
    {% if session.get('auth')['role'] == 'admin' %}
    <form action="{{ url('utilisateur/update/' ~ utilisateur.id_utilisateur) }}" method="post">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ utilisateur.username }}" required>
        </div>
        <div class="form-group">
            <label for="email">Adresse Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ utilisateur.email }}" required>
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select id="role" name="role" class="form-control" required>
                <option value="admin" {% if utilisateur.role == 'admin' %}selected{% endif %}>Admin</option>
                <option value="user" {% if utilisateur.role == 'user' %}selected{% endif %}>Utilisateur</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ url('utilisateur/index') }}" class="btn btn-secondary">Annuler</a>
    </form>
    {% else %}
    <div class="alert alert-danger mt-3">
        Vous n'avez pas l'autorisation de modifier un utilisateur.
    </div>
    {% endif %}
</div>
{% endblock %}