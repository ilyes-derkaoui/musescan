<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Musée</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0e1611;
            --panel: #132018;
            --gold: #c8a84b;
            --gold-dim: rgba(200,168,75,0.35);
            --text: #e8e4d9;
            --muted: rgba(232,228,217,0.55);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body.admin-app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
            font-family: 'Source Sans 3', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        @media (max-width: 900px) {
            body.admin-app { grid-template-columns: 1fr; }
            .admin-side { position: sticky; top: 0; z-index: 20; }
        }
        .admin-side {
            background: linear-gradient(180deg, #132016 0%, #0c140f 100%);
            border-right: 1px solid var(--gold-dim);
            padding: 28px 22px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .admin-brand {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.5);
        }
        .admin-brand strong {
            display: block;
            margin-top: 8px;
            font-size: 15px;
            letter-spacing: 0.06em;
            color: var(--gold);
            text-transform: none;
        }
        .admin-nav a {
            display: block;
            padding: 12px 14px;
            border-radius: 6px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: background 0.15s, color 0.15s;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: rgba(200,168,75,0.12);
            color: var(--text);
        }
        .admin-user {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(200,168,75,0.15);
            font-size: 13px;
            color: var(--muted);
        }
        .admin-user form { margin-top: 10px; }
        .admin-user button {
            width: 100%;
            padding: 10px;
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            border: 1px solid var(--gold-dim);
            border-radius: 6px;
            background: transparent;
            color: var(--gold);
            cursor: pointer;
        }
        .admin-user button:hover { background: rgba(200,168,75,0.1); }
        .admin-main {
            padding: clamp(20px, 4vw, 40px);
            overflow-x: auto;
        }
        .admin-main-inner { max-width: 1200px; margin: 0 auto; }
    </style>
    @stack('styles')
</head>
<body class="admin-app">
    <aside class="admin-side">
        <div class="admin-brand">
            Administration
            <strong>Collections & QR</strong>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.artifacts.index') }}" class="{{ request()->routeIs('admin.artifacts.*') ? 'active' : '' }}">Artefacts</a>
            <a href="{{ url('/') }}">Site public</a>
        </nav>
        <div class="admin-user">
            <span>{{ Auth::user()->name }}</span><br>
            <span style="font-size:12px;opacity:.8">{{ Auth::user()->email }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Déconnexion</button>
            </form>
        </div>
    </aside>
    <main class="admin-main">
        <div class="admin-main-inner">
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>
