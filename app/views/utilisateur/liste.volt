{% extends "layout/main.volt" %}

{% block content %}
{% if session.get('role') == 'admin' %}
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
        </tr>
    </thead>
    <tbody>
        {% for utilisateur in utilisateurs %}
        <tr>
            <td>{{ utilisateur.id_utilisateur }}</td>
            <td>{{ utilisateur.username }}</td>
            <td>{{ utilisateur.email }}</td>
            <td>{{ utilisateur.role }}</td>
        </tr>
        {% else %}
        <tr>
            <td colspan="4">Aucun utilisateur trouvé.</td>
        </tr>
        {% endfor %}
    </tbody>
</table>
{% else %}
<p>Accès refusé. Seul l'administrateur peut voir la liste des utilisateurs.</p>
{% endif %}
{% endblock %}