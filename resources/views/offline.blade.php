<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#004b91">
    <title>Hors ligne | {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="/images/logo-mark.svg">
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            width: 100%;
            max-width: 28rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 2.25rem 1.75rem;
            text-align: center;
            box-shadow: 0 16px 36px -16px rgba(15, 23, 42, 0.14);
        }
        .logo {
            width: 4.5rem;
            height: 4.5rem;
            margin: 0 auto 1.25rem;
            display: block;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            background: #fef3c7;
            color: #92400e;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 0.9rem;
            color: #475569;
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: opacity 150ms ease;
        }
        .btn-primary {
            background: #004b91;
            color: #ffffff;
        }
        .btn-outline {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .btn:hover { opacity: 0.92; }
        .phone {
            margin-top: 1.25rem;
            font-size: 0.8rem;
            color: #64748b;
        }
        .phone strong { color: #004b91; }
    </style>
</head>
<body>
    <main class="card">
        <img src="/images/logo-mark.svg" alt="{{ config('app.name') }}" class="logo">
        <span class="badge">Mode hors ligne</span>
        <h1>Connexion internet interrompue</h1>
        <p>
            Impossible de charger cette page pour le moment. Vérifiez votre réseau Wi-Fi ou données mobiles puis réessayez.
        </p>
        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                Réessayer la connexion
            </button>
            <a href="/" class="btn btn-outline">
                Retour à l'accueil
            </a>
        </div>
        <p class="phone">
            Besoin d'aide ? Appelez-nous au <strong>{{ config('project_configuration.phone_number') }}</strong>
        </p>
    </main>
</body>
</html>
