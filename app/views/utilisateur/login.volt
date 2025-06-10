<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Gestion Stock Vente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Connexion</h3>
                {% if flashSession.has('error') %}
                    <div class="alert alert-danger">
                        {{ flashSession.get('error') }}
                    </div>
                {% endif %}
                <form action="{{ url('auth/authenticate') }}" method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html></div></body></html>