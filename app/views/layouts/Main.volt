<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Gestion Stock & Vente{% endblock %}</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white p-3 mb-4">
        <div class="container">
            <h1>Gestion Stock & Vente</h1>
        </div>
    </header>

    <!-- Navbar/Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Accueil</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    {% if adminLinks is defined %}
                        {% for link in adminLinks %}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url(link.url) }}">{{ link.label }}</a>
                            </li>
                        {% endfor %}
                    {% endif %}
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
    <div class="container">
        {% for type, messages in flashSession.getMessages() %}
            {% for message in messages %}
                <div class="alert alert-{{ type }} mt-2">{{ message }}</div>
            {% endfor %}
        {% endfor %}
    </div>

    <!-- Main Content -->
    <main class="container mb-4">
        {% block content %}{% endblock %}
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center p-3">
        &copy; {{ "now"|date("Y") }} Gestion Stock & Vente
    </footer>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>