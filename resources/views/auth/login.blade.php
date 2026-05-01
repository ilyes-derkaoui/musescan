<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Cormorant+Garamond:ital@0;1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Cormorant Garamond', Georgia, serif;
            background: radial-gradient(ellipse at 30% 20%, rgba(200,168,75,0.12), transparent 50%),
                linear-gradient(165deg, #0e1b10 0%, #132016 45%, #0a120c 100%);
            color: #f3ecd9;
        }
        .card {
            width: 100%;
            max-width: 420px;
            padding: 36px 32px 40px;
            border: 1px solid rgba(200,168,75,0.28);
            border-radius: 4px;
            background: rgba(19, 32, 22, 0.92);
            box-shadow: 0 24px 60px rgba(0,0,0,0.45);
        }
        .brand {
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.55);
            margin-bottom: 8px;
        }
        h1 {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 600;
            color: #dfc278;
            margin-bottom: 28px;
        }
        label {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.5);
            margin-bottom: 8px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid rgba(200,168,75,0.25);
            border-radius: 2px;
            background: rgba(0,0,0,0.25);
            color: #f3ecd9;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: rgba(200,168,75,0.55);
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            font-size: 14px;
            color: rgba(255,255,255,0.55);
        }
        .remember input { width: auto; }
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            font-family: 'Cinzel', serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            border: none;
            border-radius: 2px;
            background: #c8a84b;
            color: #1c1406;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover { background: #dfc278; }
        .error {
            background: rgba(248,113,113,0.12);
            border: 1px solid rgba(248,113,113,0.35);
            color: #fecaca;
            padding: 10px 12px;
            border-radius: 2px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .back {
            display: inline-block;
            margin-top: 22px;
            font-size: 14px;
            color: rgba(200,168,75,0.45);
            text-decoration: none;
        }
        .back:hover { color: #dfc278; }
    </style>
</head>
<body>
    <div class="card">
        <p class="brand">Musée — Administration</p>
        <h1>Connexion</h1>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Courriel</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>

            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                Se souvenir de moi
            </label>

            <button type="submit">Entrer</button>
        </form>

        <a class="back" href="{{ url('/') }}">← Retour au site</a>
    </div>
</body>
</html>
