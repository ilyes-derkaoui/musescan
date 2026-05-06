{{--
|--------------------------------------------------------------------------
| welcome.blade.php — Musée de l'Armée Centrale
| Placement : resources/views/welcome.blade.php
|
| ASSETS REQUIS :
|   resources/images/anp.png   → Logo Ministère de la Défense Nationale
|   resources/images/dic.png   → Logo Direction Information & Communication
|   resources/images/bg.jpg    → Photo intérieure du musée (hero background)
|
| DÉPENDANCE CDN :
|   jsQR 1.4.0 — décodage QR code réel via caméra + canvas
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>المتحف المركزي للجيش — Musée de l'armée centrale</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet" />

    {{-- jsQR : décodage QR réel via canvas --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsQR.js"></script>

    {{-- model-viewer : rendu 3D GLB/GLTF (Google WebXR, pas de build) --}}
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>

    <style>
        /* ============================================================
           RESET & VARIABLES
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --header-h: 64px;
            --hdr-green:  #132016;
            --dark-green: #0e1b10;
            --alg-green:  #0f5b2f;
            --alg-red:    #b02a2a;
            --gold:       #c8a84b;
            --gold-lt:    #dfc278;
            --gold-deep:  #a8842e;
            --gold-dim:   rgba(200,168,75,0.15);
            --ivory:      #f3ecd9;
            --sand:       #e9dcc1;
            --ink:        #1c1406;
            --ink-mid:    #3a2e18;
            --muted-gold: rgba(200,168,75,0.6);
            --btn-shadow: 0 8px 24px rgba(0,0,0,0.28);
            --fa: 'Amiri', 'Traditional Arabic', serif;
            --ff: 'Cormorant Garamond', Georgia, serif;
            --fh: 'Cinzel', 'Times New Roman', serif;
            --fu: 'EB Garamond', Georgia, serif;
        }

        body {
            font-family: var(--fu);
            background: var(--dark-green);
            color: #fff;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            text-size-adjust: 100%;
        }

        /* ============================================================
           HEADER
        ============================================================ */
        .hdr {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            height: var(--header-h);
            background: var(--hdr-green);
            border-bottom: 1px solid rgba(200,168,75,0.2);
            backdrop-filter: blur(6px);
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            padding: 0 clamp(14px, 3vw, 36px);
        }

        .hdr-block {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .hdr-block.l { justify-self: start; }
        .hdr-block.r {
            flex-direction: row-reverse;
            justify-self: end;
            text-align: right;
        }

        .hdr-site-lang {
            justify-self: center;
            position: relative;
            flex-shrink: 0;
        }

        .hdr-lang-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 34px;
            padding: 0 12px 0 10px;
            border-radius: 999px;
            border: 1px solid rgba(200,168,75,0.38);
            background: rgba(8, 22, 14, 0.55);
            color: rgba(243,236,217,0.95);
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.06),
                0 4px 14px rgba(0,0,0,0.25);
            transition: border-color 0.2s, background-color 0.2s, box-shadow 0.2s;
        }

        .hdr-lang-btn:hover {
            border-color: rgba(223,194,120,0.55);
            background: rgba(14, 35, 22, 0.72);
        }

        .hdr-lang-btn svg { flex-shrink: 0; opacity: 0.75; }

        .hdr-lang-code {
            min-width: 22px;
            text-align: center;
            letter-spacing: 0.12em;
        }

        .hdr-lang-chevron {
            font-size: 9px;
            opacity: 0.55;
            margin-left: -2px;
        }

        .hdr-lang-sheet {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            min-width: 208px;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid rgba(200,168,75,0.28);
            background: rgba(10, 28, 18, 0.98);
            backdrop-filter: blur(10px);
            box-shadow:
                0 16px 40px rgba(0,0,0,0.45),
                inset 0 1px 0 rgba(255,255,255,0.05);
            display: none;
            z-index: 120;
        }

        .hdr-site-lang.open .hdr-lang-sheet { display: block; }

        .hdr-lang-row {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 9px 12px;
            background: transparent;
            color: rgba(255,255,255,0.88);
            font-family: var(--fu);
            font-size: 13px;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .hdr-lang-row:hover { background: rgba(200,168,75,0.1); color: var(--gold-lt); }

        .hdr-lang-row.active {
            background: rgba(200,168,75,0.14);
            color: var(--gold);
        }

        .hdr-lang-row-flag { font-size: 17px; line-height: 1; width: 24px; text-align: center; }
        .hdr-lang-row-name { flex: 1; min-width: 0; }
        .hdr-lang-row-mini {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: rgba(200,168,75,0.55);
        }

        .hdr-lang-row.active .hdr-lang-row-mini { color: var(--gold-lt); }

        .hdr-ar {
            font-family: var(--fa);
            font-size: clamp(9px, 1.05vw, 12px);
            font-weight: 700;
            color: rgba(255,255,255,0.88);
            direction: rtl;
            line-height: 1.3;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: min(220px, 28vw);
        }

        .hdr-block.r .hdr-ar { direction: rtl; text-align: right; }

        .emblem {
            width: 56px; height: 56px;
            border-radius: 50%;
            border: 1px solid var(--muted-gold);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(200,168,75,0.06);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 3px rgba(200,168,75,0.08), 0 8px 18px rgba(0,0,0,0.35);
        }

        .emblem img {
            width: 100%; height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transform: scale(1.07);
            filter: contrast(1.08) saturate(0.95);
        }

        .emblem-fallback {
            width: 22px; height: 22px;
            stroke: var(--muted-gold);
            fill: none;
            stroke-width: 0.9;
        }

        .hdr-txt { line-height: 1.35; }
        .hdr-t1 {
            font-family: var(--fh);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: var(--gold);
        }
        .hdr-t2 {
            font-family: var(--fu);
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: var(--muted-gold);
        }
        .hdr-block.r .hdr-txt { text-align: right; }

        /* ============================================================
           HERO
        ============================================================ */
        .hero {
            position: relative;
            /* min-height only: fixed height + overflow:hidden was clipping the lower buttons
               so the ivory section appeared to “cover” Administration */
            min-height: max(700px, calc(var(--vh, 1vh) * 100));
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow-x: hidden;
            overflow-y: visible;
            padding-top: var(--header-h);
            padding-bottom: clamp(56px, 12vh, 140px);
            background: #051c13;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 50% 0%, rgba(200,168,75,0.09), transparent 46%),
                repeating-linear-gradient(
                    0deg,
                    rgba(255,255,255,0.006) 0px,
                    rgba(255,255,255,0.006) 1px,
                    transparent 1px,
                    transparent 3px
                );
            z-index: 1;
        }

        /*
        ┌──────────────────────────────────────────┐
        │  BG IMAGE : resources/images/bg.jpg      │
        └──────────────────────────────────────────┘
        */
        .hero-bg {
            position: absolute;
            top: var(--header-h);
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: url('{{ asset("images/bg.jpg") }}') center center / cover no-repeat;
            filter: brightness(0.28) saturate(0.6);
            border: none;
        }

        .hero-veil {
            position: absolute; inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(7,23,14,0.4) 0%,
                rgba(7,23,14,0.2) 38%,
                rgba(7,23,14,0.78) 100%
            );
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 560px;
            padding: 0 28px;
            margin-top: 38px;
            animation: up .7s ease both;
        }

        @keyframes up {
            from { opacity:0; transform: translateY(22px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .hero-republic-line {
            font-family: var(--fh);
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--muted-gold);
            margin-bottom: 18px;
        }

        body.site-lang-ar .hero-republic-line {
            font-family: var(--fa);
            font-size: clamp(14px,1.9vw,20px);
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
            color: rgba(255,255,255,0.72);
            direction: rtl;
        }

        /* Séparateur doré ♦ */
        .div-gold {
            display: flex; align-items: center; justify-content: center;
            gap: 12px;
            margin: 0 auto 22px;
            width: fit-content;
        }

        .dg-line {
            width: 56px; height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold));
        }
        .dg-line.r { background: linear-gradient(90deg, var(--gold), transparent); }

        .dg-diamond {
            width: 7px; height: 7px;
            background: var(--gold);
            transform: rotate(45deg);
            flex-shrink: 0;
        }

        .hero-title-line {
            font-family: var(--ff);
            font-size: clamp(26px,4.1vw,54px);
            font-weight: 400;
            font-style: italic;
            color: var(--gold-lt);
            margin-bottom: 18px;
            text-shadow: 0 0 12px rgba(200,168,75,0.16);
            line-height: 1.18;
        }

        body.site-lang-ar .hero-title-line {
            font-family: var(--fa);
            font-size: clamp(48px,7vw,76px);
            font-style: normal;
            font-weight: 700;
            color: #fff;
            direction: rtl;
            text-shadow: 0 2px 18px rgba(0,0,0,0.36);
        }

        body.site-lang-ar .hero-content { direction: rtl; }
        body.site-lang-ar .hero-btns { direction: rtl; }

        .hero-desc {
            font-family: var(--ff);
            font-size: clamp(13px,1.5vw,17px);
            font-style: italic;
            font-weight: 300;
            color: rgba(255,255,255,0.6);
            line-height: 1.8;
            max-width: 500px;
            margin: 0 auto 36px;
        }

        /* Boutons */
        .hero-btns {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .hbtn {
            width: 100%;
            max-width: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-family: var(--fh);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 18px 32px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, filter 0.2s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            min-height: 52px;
            touch-action: manipulation;
            transform: translateY(0);
        }

        .hbtn-gold {
            background: linear-gradient(135deg, var(--gold-lt) 0%, var(--gold) 42%, var(--gold-deep) 100%);
            color: #1c1406;
            border-color: rgba(28,20,6,0.18);
            box-shadow: var(--btn-shadow);
        }
        .hbtn-gold:hover {
            filter: saturate(1.05) brightness(1.02);
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.34);
        }

        .hbtn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -120%;
            width: 70%;
            height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.55s ease;
        }

        .hbtn:hover::before {
            left: 130%;
        }

        .hbtn-gold svg {
            width: 18px; height: 18px;
            fill: currentColor;
        }

        .hbtn-outline {
            background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
            color: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.34);
            box-shadow: inset 0 0 0 1px rgba(200,168,75,0.06);
        }
        .hbtn-outline:hover {
            border-color: var(--gold);
            color: #fff;
            background: rgba(200,168,75,0.1);
            transform: translateY(-1px);
        }

        /* Troisième bouton : même famille, accent doré */
        .hbtn-admin {
            border-color: rgba(176,42,42,0.45);
            color: var(--gold-lt);
            letter-spacing: 0.16em;
            background:
                radial-gradient(circle at 14% 15%, rgba(176,42,42,0.16), transparent 48%),
                rgba(255,255,255,0.02);
        }
        .hbtn-admin:hover {
            border-color: rgba(176,42,42,0.72);
            color: #fff;
            background:
                radial-gradient(circle at 14% 15%, rgba(176,42,42,0.22), transparent 52%),
                rgba(200,168,75,0.12);
        }

        .hbtn:focus-visible,
        .map-floor-btn:focus-visible,
        #qrBtn:focus-visible,
        .qr-alt-btn:focus-visible,
        .art-back:focus-visible,
        .lang-drop-btn:focus-visible,
        .lang-btn:focus-visible,
        .art-narrate:focus-visible {
            outline: 2px solid var(--sand);
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(15,91,47,0.22);
        }

        /* Chevron bas */
        .hero-chevron {
            position: absolute;
            bottom: 18px; left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            background: none;
            border: none;
            color: var(--muted-gold);
            font-size: 24px;
            cursor: pointer;
            animation: bounce 2.2s ease-in-out infinite;
            min-height: 44px;
            min-width: 44px;
            touch-action: manipulation;
            border-radius: 999px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .hero-chevron:hover {
            color: var(--gold-lt);
            background: rgba(200,168,75,0.08);
        }

        @keyframes bounce {
            0%,100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(8px); }
        }

        /* ============================================================
           SECTION ORGANIGRAMME (fond ivoire)
        ============================================================ */
        .sec-org {
            position: relative;
            z-index: 1;
            background: var(--ivory);
            /* texture légère */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='6' height='6'%3E%3Crect width='6' height='6' fill='%23f3ecd9'/%3E%3Ccircle cx='1' cy='1' r='0.6' fill='%23ddd0b8' opacity='0.35'/%3E%3C/svg%3E");
            padding: 104px clamp(24px,6vw,80px);
            text-align: center;
        }

        .org-line {
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--ink-mid);
            line-height: 1.45;
        }

        body.site-lang-ar .org-line {
            font-family: var(--fa);
            font-size: clamp(22px,3.5vw,38px);
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
            color: var(--ink);
            direction: rtl;
        }

        .org-sep {
            display: flex; align-items: center; justify-content: center;
            gap: 16px;
            margin: 38px auto;
            width: fit-content;
        }

        .os-line {
            width: 80px; height: 1px;
            background: var(--gold);
            opacity: 0.55;
        }

        .os-diamond {
            width: 7px; height: 7px;
            background: var(--gold);
            transform: rotate(45deg);
            flex-shrink: 0;
        }

        /* ============================================================
           SECTION PLAN DU MUSEE
        ============================================================ */
        .sec-map {
            background: var(--ivory);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%23f3ecd9'/%3E%3Cpath d='M0 7h8' stroke='%23e2d6be' stroke-opacity='0.35'/%3E%3C/svg%3E");
            border-top: 1px solid rgba(200,168,75,0.2);
            padding: 78px clamp(18px, 6vw, 80px) 90px;
        }

        .map-wrap {
            max-width: 980px;
            margin: 0 auto;
        }

        .map-head {
            text-align: center;
            margin-bottom: 34px;
        }

        .map-headline {
            font-family: var(--fh);
            font-size: clamp(11px, 1.05vw, 12px);
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-mid);
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.4;
        }

        body.site-lang-ar .map-headline {
            font-family: var(--fa);
            font-size: clamp(24px, 4vw, 40px);
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
            color: var(--ink);
            direction: rtl;
        }

        body.site-lang-ar .map-panel,
        body.site-lang-ar .map-panel h3,
        body.site-lang-ar .map-panel-hint,
        body.site-lang-ar .map-note {
            direction: rtl;
            text-align: right;
        }

        body.site-lang-ar .map-panel h3 {
            font-style: normal;
        }

        body.site-lang-ar .sec-map .map-title {
            direction: rtl;
            right: 20px;
            left: auto;
            text-align: right;
            font-style: normal;
            font-family: var(--fa);
        }

        .map-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 18px;
            align-items: stretch;
        }

        .map-board {
            position: relative;
            min-height: 320px;
            border: 1px solid rgba(200,168,75,0.35);
            background:
                linear-gradient(180deg, rgba(200,168,75,0.035), rgba(200,168,75,0.018)),
                repeating-linear-gradient(
                    0deg,
                    rgba(28,20,6,0.04) 0px,
                    rgba(28,20,6,0.04) 1px,
                    transparent 1px,
                    transparent 32px
                ),
                repeating-linear-gradient(
                    90deg,
                    rgba(28,20,6,0.035) 0px,
                    rgba(28,20,6,0.035) 1px,
                    transparent 1px,
                    transparent 32px
                );
            overflow: hidden;
        }

        .map-board::before {
            content: "";
            position: absolute;
            inset: 10px;
            border: 1px dashed rgba(200,168,75,0.35);
            pointer-events: none;
        }

        .map-title {
            position: absolute;
            top: 18px;
            left: 20px;
            z-index: 4;
            font-family: var(--ff);
            font-style: italic;
            font-size: 20px;
            color: rgba(28,20,6,0.72);
        }

        .map-lines {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .map-lines path {
            stroke: rgba(200,168,75,0.55);
            stroke-width: 2;
            fill: none;
            stroke-dasharray: 6 8;
        }

        .map-panel {
            border: 1px solid rgba(200,168,75,0.35);
            background: rgba(255,255,255,0.36);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
        }

        .map-panel h3 {
            font-family: var(--ff);
            font-size: 27px;
            font-style: italic;
            font-weight: 500;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .map-panel-hint {
            font-family: var(--fu);
            font-size: 13px;
            color: rgba(58,46,24,0.75);
            margin-bottom: 12px;
            line-height: 1.45;
        }

        .map-floor-btns {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .map-floor-btn {
            display: block;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border: 1px solid rgba(200,168,75,0.34);
            background: rgba(200,168,75,0.045);
            color: var(--ink-mid);
            padding: 13px 14px;
            border-radius: 10px;
            font-family: var(--fu);
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s, transform 0.18s ease;
            touch-action: manipulation;
        }

        .map-floor-btn:hover {
            border-color: rgba(200,168,75,0.5);
            background: rgba(200,168,75,0.09);
            transform: translateY(-1px);
        }

        .map-floor-btn.active {
            border-color: rgba(200,168,75,0.65);
            background:
                linear-gradient(90deg, rgba(176,42,42,0.08), rgba(200,168,75,0.16) 32%, rgba(200,168,75,0.12));
            box-shadow: 0 0 0 1px rgba(200,168,75,0.2), 0 8px 18px rgba(28,20,6,0.12);
        }

        .map-floor-label {
            display: block;
            font-family: var(--fh);
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--ink);
            line-height: 1.35;
        }

        body.site-lang-ar .map-floor-label {
            font-family: var(--fa);
            font-size: 13px;
            letter-spacing: 0;
            text-transform: none;
            direction: rtl;
            font-weight: 600;
        }

        body.site-lang-ar .map-floor-btn {
            text-align: right;
        }

        .map-points-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
        }

        .map-point {
            position: absolute;
            transform: translate(-50%, -50%);
            z-index: 3;
        }

        .map-point-dot {
            display: block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 0 4px rgba(200,168,75,0.14);
            margin: 0 auto;
        }

        .map-point-label {
            position: absolute;
            top: 14px;
            left: 50%;
            transform: translateX(-50%);
            min-width: max-content;
            max-width: 140px;
            text-align: center;
            padding: 5px 8px;
            border-radius: 3px;
            background: rgba(255,253,248,0.94);
            border: 1px solid rgba(200,168,75,0.4);
            box-shadow: 0 4px 12px rgba(28,20,6,0.12);
        }

        .map-point-name {
            display: block;
            font-family: var(--ff);
            font-size: 11px;
            font-style: italic;
            font-weight: 500;
            color: var(--ink);
            line-height: 1.25;
            text-align: center;
        }

        body.site-lang-ar .map-point-name {
            font-family: var(--fa);
            font-style: normal;
            font-weight: 700;
            direction: rtl;
        }

        .map-note {
            margin-top: 14px;
            font-family: var(--fu);
            font-size: 14px;
            color: rgba(58,46,24,0.86);
            line-height: 1.5;
        }
        /* ============================================================
           FOOTER
        ============================================================ */
        .ftr {
            background: var(--hdr-green);
            border-top: 1px solid rgba(200,168,75,0.28);
            padding: 52px 40px 44px;
            text-align: center;
        }

        .ftr-deco {
            display: flex; align-items: center; justify-content: center;
            gap: 14px;
            margin-bottom: 26px;
        }

        .fd-line { width: 70px; height: 1px; }
        .fd-line.l { background: linear-gradient(90deg, transparent, var(--gold)); }
        .fd-line.r { background: linear-gradient(90deg, var(--gold), transparent); }

        .fd-diamond {
            width: 7px; height: 7px;
            background: var(--gold);
            transform: rotate(45deg);
        }

        .ftr-title-line {
            font-family: var(--ff);
            font-size: 15px;
            font-style: italic;
            color: var(--muted-gold);
            margin-bottom: 26px;
            text-shadow: 0 0 10px rgba(200,168,75,0.1);
        }

        body.site-lang-ar .ftr-title-line {
            font-family: var(--fa);
            font-size: 18px;
            font-style: normal;
            color: var(--gold-lt);
            direction: rtl;
        }

        .ftr-copy {
            font-family: var(--fh);
            font-size: 10px;
            font-weight: 400;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.3);
        }

        /* ============================================================
           MODAL QR SCANNER
        ============================================================ */
        .qr-ov {
            position: fixed; inset: 0;
            z-index: 300;
            background: rgba(8,18,10,0.93);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-top: max(20px, env(safe-area-inset-top));
            padding-bottom: max(20px, env(safe-area-inset-bottom));
        }

        .qr-ov.open { display: flex; }

        .qr-box {
            background: var(--hdr-green);
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 4px;
            width: 100%;
            max-width: 420px;
            max-height: min(92vh, 780px);
            overflow: hidden;
            animation: pop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
        }

        @keyframes pop {
            from { opacity:0; transform: scale(0.91) translateY(14px); }
            to   { opacity:1; transform: scale(1) translateY(0); }
        }

        .qr-top {
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(200,168,75,0.1);
        }

        .qr-top-title {
            font-family: var(--ff);
            font-size: 18px;
            font-style: italic;
            color: var(--gold-lt);
        }

        .qr-x {
            width: 30px; height: 30px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: rgba(255,255,255,0.45);
            font-size: 16px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.18s;
        }

        .qr-x:hover { background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(200,168,75,0.34); }

        .qr-body { padding: 20px; }

        /* Status */
        .qr-stat {
            display: flex; align-items: center;
            gap: 9px; margin-bottom: 14px;
        }

        .qr-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            flex-shrink: 0;
            transition: background 0.25s;
        }

        .qr-dot.scanning {
            background: #4ade80;
            box-shadow: 0 0 8px rgba(74,222,128,0.5);
            animation: blink 1.4s infinite;
        }

        .qr-dot.ok { background: var(--gold); box-shadow: 0 0 8px rgba(200,168,75,0.45); }
        .qr-dot.err { background: #f87171; }

        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.45} }

        .qr-stat-txt {
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            transition: color 0.25s;
        }

        /* Vidéo zone */
        .qr-vid-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            background: #000;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        #qrVideo {
            width: 100%; height: 100%;
            object-fit: cover;
            display: none;
        }

        /* Video zone */
        .qr-idle {
            width: 100%; height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px; padding: 20px;
        }

        .qr-idle svg {
            width: 54px; height: 54px;
            stroke: rgba(200,168,75,0.28);
            fill: none; stroke-width: 0.8;
        }

        .qr-idle p {
            font-family: var(--fu);
            font-size: 12px;
            color: rgba(255,255,255,0.18);
            text-align: center;
            line-height: 1.65;
        }

        /* Cadre de scan animé */
        .qr-frame {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            width: 60%; aspect-ratio: 1;
            display: none; pointer-events: none;
        }

        .qfc {
            position: absolute;
            width: 20px; height: 20px;
            border-color: var(--gold);
            border-style: solid;
        }

        .qfc.tl { top:0; left:0;  border-width: 2px 0 0 2px; }
        .qfc.tr { top:0; right:0; border-width: 2px 2px 0 0; }
        .qfc.bl { bottom:0; left:0; border-width: 0 0 2px 2px; }
        .qfc.br { bottom:0; right:0; border-width: 0 2px 2px 0; }

        .qr-beam {
            position: absolute;
            left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            top: 0;
            animation: beam 2s ease-in-out infinite;
        }

        @keyframes beam {
            0%  { top:4%;  opacity:0; }
            8%  { opacity:1; }
            92% { opacity:1; }
            100%{ top:96%; opacity:0; }
        }

        /* Résultat détecté */
        .qr-res {
            display: none;
            background: rgba(200,168,75,0.07);
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 3px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .qr-res-lbl {
            font-family: var(--fu);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px;
        }

        .qr-res-val {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
            word-break: break-all;
        }

        /* Bouton */
        #qrBtn {
            width: 100%;
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink);
            background: linear-gradient(135deg, var(--gold-lt), var(--gold) 45%, var(--gold-deep));
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.18s ease, filter 0.2s ease, box-shadow 0.2s ease;
            margin-top: 4px;
            min-height: 50px;
            touch-action: manipulation;
            box-shadow: var(--btn-shadow);
        }

        #qrBtn:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(0,0,0,0.3);
        }
        #qrBtn:disabled { opacity: 0.35; cursor: not-allowed; }

        .qr-note {
            font-family: var(--fu);
            font-size: 11px;
            color: rgba(255,255,255,0.2);
            text-align: center;
            line-height: 1.7;
            margin-top: 10px;
        }

        .qr-alt {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(200,168,75,0.12);
        }
        .qr-alt-lbl {
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.45);
            margin-bottom: 10px;
        }
        .qr-alt-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: stretch;
            margin-bottom: 10px;
        }
        .qr-alt-row input[type="text"] {
            flex: 1;
            min-width: 140px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            padding: 10px 12px;
            border: 1px solid rgba(200,168,75,0.25);
            border-radius: 3px;
            background: rgba(0,0,0,0.25);
            color: rgba(255,255,255,0.88);
        }
        .qr-alt-btn {
            font-family: var(--fu);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(200,168,75,0.35);
            background: rgba(200,168,75,0.08);
            color: var(--gold-lt);
            cursor: pointer;
            white-space: nowrap;
            touch-action: manipulation;
            transition: transform 0.18s ease, background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .qr-alt-btn:hover {
            background: rgba(200,168,75,0.15);
            border-color: rgba(200,168,75,0.52);
            transform: translateY(-1px);
        }
        .qr-alt-btn.primary {
            background: linear-gradient(135deg, var(--gold-lt), var(--gold));
            color: var(--ink);
            border-color: var(--gold);
        }
        .qr-alt-btn.primary:hover { filter: brightness(1.02); }
        #qrFileInput { display: none; }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 600px) {
            :root { --header-h: 60px; }
            .hdr { padding: 0 8px; gap: 6px; grid-template-columns: minmax(0,1fr) auto minmax(0,1fr); }
            .hdr-block { gap: 8px; }
            .hdr-t1, .hdr-t2 { font-size: 8px; }
            .hdr-ar {
                font-size: 8px;
                max-width: min(100px, 24vw);
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .hdr-lang-btn { height: 32px; padding: 0 10px 0 8px; gap: 6px; font-size: 9px; }
            .hdr-lang-sheet { min-width: 186px; }
            .emblem { width: 40px; height: 40px; }
            .hero {
                min-height: calc(var(--vh, 1vh) * 100);
                padding-top: calc(var(--header-h) + env(safe-area-inset-top));
            }
            .hero-content {
                width: 100%;
                margin-top: 14px;
                padding: 0 16px;
            }
            .hero-bg { width: 100vw; border: none; }
            body.site-lang-ar .hero-title-line { font-size: clamp(36px, 13vw, 56px); }
            body:not(.site-lang-ar) .hero-title-line { font-size: clamp(28px, 10vw, 40px); }
            .hero-desc { font-size: 15px; line-height: 1.55; margin-bottom: 22px; }
            .hbtn { max-width: 100%; font-size: 11px; min-height: 52px; padding: 16px 18px; letter-spacing: 0.16em; }
            .map-floor-btn { min-height: 48px; }
            .lang-btn { min-height: 38px; padding: 7px 12px; }
            .hero-chevron { bottom: 10px; }
            .sec-org { padding: 64px 18px; }
            .os-line { width: 52px; }
            .sec-map { padding: 56px 16px 64px; }
            .map-grid { grid-template-columns: 1fr; }
            .map-board { min-height: 270px; }
            .map-panel h3 { font-size: 24px; }
            .ftr { padding: 40px 18px calc(32px + env(safe-area-inset-bottom)); }
            .qr-ov {
                align-items: flex-end;
                padding: 0;
                padding-bottom: env(safe-area-inset-bottom);
            }
            .qr-box {
                max-width: 100%;
                border-left: none;
                border-right: none;
                border-bottom: none;
                border-radius: 16px 16px 0 0;
                max-height: calc(var(--vh, 1vh) * 92);
            }
            .qr-body { padding: 14px; }
        }

        @media (hover: none) {
            .hbtn::before { display: none; }
            .hbtn:active {
                transform: scale(0.992);
                filter: brightness(0.995);
            }
            .map-floor-btn:active,
            #qrBtn:active,
            .qr-alt-btn:active,
            .art-back:active,
            .lang-btn:active,
            .art-narrate:active {
                transform: scale(0.99);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hbtn,
            .map-floor-btn,
            #qrBtn,
            .qr-alt-btn,
            .art-back,
            .lang-drop-btn,
            .lang-btn,
            .art-narrate,
            .hero-chevron {
                transition: none !important;
                animation: none !important;
            }
        }

        /* ============================================================
           ARTIFACT OVERLAY — redesign
        ============================================================ */
        .art-ov {
            position: fixed; inset: 0;
            z-index: 400;
            background: var(--dark-green);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: art-in 0.38s cubic-bezier(0.22,1,0.36,1) both;
        }
        .art-ov.open { display: flex; }

        @keyframes art-in {
            from { opacity:0; transform: translateY(30px); }
            to   { opacity:1; transform: translateY(0); }
        }

        /* Top bar */
        .art-bar {
            flex-shrink: 0;
            height: 58px;
            background: rgba(10,20,12,0.97);
            border-bottom: 1px solid rgba(200,168,75,0.18);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            gap: 14px;
            backdrop-filter: blur(10px);
        }

        .art-back {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(200,168,75,0.25);
            color: var(--gold);
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: transform 0.18s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
        }
        .art-back:hover {
            background: var(--gold-dim);
            border-color: var(--gold);
            transform: translateY(-1px);
        }

        /* Language dropdown */
        .lang-drop {
            position: relative;
        }
        .lang-drop-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--fh);
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            padding: 7px 14px;
            border: 1px solid rgba(200,168,75,0.3);
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(200,168,75,0.1), rgba(200,168,75,0.04));
            color: var(--gold-lt);
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .lang-drop-btn:hover { background: rgba(200,168,75,0.12); border-color: var(--gold); }
        .lang-drop-btn svg { transition: transform 0.2s; }
        .lang-drop.open .lang-drop-btn svg { transform: rotate(180deg); }

        .lang-drop-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 160px;
            background: #0d1f0f;
            border: 1px solid rgba(200,168,75,0.2);
            border-radius: 3px;
            overflow: hidden;
            z-index: 500;
            display: none;
            box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        }
        .lang-drop.open .lang-drop-menu { display: block; }

        .lang-opt {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 16px;
            font-family: var(--fu);
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            cursor: pointer;
            border: none;
            background: none;
            text-align: left;
            transition: all 0.15s;
            border-bottom: 1px solid rgba(200,168,75,0.06);
        }
        .lang-opt:last-child { border-bottom: none; }
        .lang-opt:hover { background: rgba(200,168,75,0.08); color: var(--gold-lt); }
        .lang-opt.active { background: rgba(200,168,75,0.12); color: var(--gold); }
        .lang-opt .lang-flag { font-size: 16px; }
        .lang-opt .lang-name { flex: 1; }
        .lang-opt .lang-check { color: var(--gold); font-size: 12px; opacity: 0; }
        .lang-opt.active .lang-check { opacity: 1; }

        /* Language pill buttons in the top bar */
        .art-lang-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            justify-content: center;
            flex-wrap: nowrap;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(200,168,75,0.3) transparent;
            padding: 2px 0;
        }

        .lang-btn {
            font-family: var(--fu);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            padding: 7px 14px;
            flex-shrink: 0;
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 20px;
            background: rgba(200,168,75,0.05);
            color: rgba(255,255,255,0.45);
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, color 0.18s ease, background-color 0.18s ease;
            white-space: nowrap;
            min-height: 36px;
            touch-action: manipulation;
        }
        .lang-btn:hover {
            border-color: rgba(200,168,75,0.55);
            color: var(--gold-lt);
            background: rgba(200,168,75,0.1);
            transform: translateY(-1px);
        }
        .lang-btn.active {
            background: linear-gradient(135deg, var(--gold-lt), var(--gold));
            border-color: var(--gold);
            color: var(--ink);
        }
        .lang-btn.active:hover {
            background: var(--gold-lt);
            border-color: var(--gold-lt);
        }

        .art-narrate {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(200,168,75,0.08);
            border: 1px solid rgba(200,168,75,0.3);
            color: var(--gold-lt);
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: transform 0.18s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
            min-height: 36px;
            touch-action: manipulation;
        }
        .art-narrate:hover {
            background: rgba(200,168,75,0.16);
            transform: translateY(-1px);
        }
        .art-narrate.speaking {
            background: rgba(200,168,75,0.18);
            border-color: var(--gold);
            animation: pulse-btn 1.4s ease-in-out infinite;
        }
        @keyframes pulse-btn {
            0%,100% { box-shadow: 0 0 0 0 rgba(200,168,75,0.3); }
            50%      { box-shadow: 0 0 0 6px rgba(200,168,75,0); }
        }

        /* ── Body (professional single flow) ── */
        .art-body {
            flex: 1;
            overflow-y: auto;
            padding: 18px clamp(12px, 2vw, 28px) 24px;
            display: grid;
            gap: 16px;
            scrollbar-width: thin;
            scrollbar-color: rgba(200,168,75,0.25) transparent;
            background:
                radial-gradient(circle at 0% 0%, rgba(200,168,75,0.08), transparent 35%),
                radial-gradient(circle at 100% 100%, rgba(200,168,75,0.06), transparent 40%),
                linear-gradient(180deg, #071209 0%, #060e07 100%);
        }

        .art-head {
            border: 1px solid rgba(200,168,75,0.26);
            border-radius: 14px;
            padding: 16px 18px;
            background: linear-gradient(180deg, rgba(16,35,22,0.9), rgba(10,24,16,0.94));
            box-shadow: 0 12px 30px rgba(0,0,0,0.35);
        }

        .art-head-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .art-date-line {
            font-family: var(--fh);
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(223,194,120,0.9);
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(200,168,75,0.35);
            background: rgba(200,168,75,0.08);
        }

        /* Main split: viewer ~60% · description ~40% (LTR locks layout under Arabic UI) */
        .art-main-split {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
            gap: clamp(14px, 2.2vw, 24px);
            align-items: stretch;
            direction: ltr;
            width: 100%;
        }

        .art-col-visual {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .art-col-desc {
            min-width: 0;
            display: flex;
            flex-direction: column;
            direction: ltr;
        }

        .art-col-desc .art-desc-panel {
            flex: 1;
            justify-content: flex-start;
            min-height: min(320px, 42vh);
        }

        /* 3D viewer (for non-figure artifacts) */
        .art-viewer {
            flex: 1;
            width: 100%;
            min-height: min(62vh, 540px);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 14px;
            border: 1px solid rgba(200,168,75,0.28);
            box-shadow: 0 20px 50px rgba(0,0,0,0.45);
            background:
                radial-gradient(circle at 20% 15%, rgba(200,168,75,0.14), transparent 35%),
                radial-gradient(circle at 85% 85%, rgba(200,168,75,0.12), transparent 45%),
                linear-gradient(180deg, #09150b 0%, #070f08 100%);
        }
        .art-viewer model-viewer {
            width: 100%; height: 100%;
            background-color: transparent;
            --progress-bar-color: var(--gold);
        }
        .art-image-stage {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            z-index: 3;
            pointer-events: none;
            padding: 28px;
            background: radial-gradient(ellipse at center, rgba(9,22,13,0.35), rgba(7,12,8,0.8));
        }
        .art-image-stage[hidden] { display: none; }
        .art-main-image {
            width: min(75%, 520px);
            max-height: 84%;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(223,194,120,0.55);
            box-shadow:
                0 30px 70px rgba(0,0,0,0.62),
                0 0 0 6px rgba(200,168,75,0.08);
            filter: saturate(0.95) contrast(1.04);
        }
        .art-gallery-strip {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow-x: auto;
            padding: 10px;
            border-radius: 12px;
            background: rgba(7,14,9,0.7);
            border: 1px solid rgba(200,168,75,0.24);
            backdrop-filter: blur(6px);
            scrollbar-width: thin;
            scrollbar-color: rgba(200,168,75,0.35) transparent;
        }
        .art-gallery-strip:empty { display: none; }
        .art-gallery-thumb {
            width: 92px;
            height: 68px;
            object-fit: cover;
            border-radius: 9px;
            border: 1px solid rgba(200,168,75,0.25);
            cursor: pointer;
            opacity: 0.72;
            flex-shrink: 0;
            transition: opacity 0.16s ease, border-color 0.16s ease, transform 0.16s ease;
        }
        .art-gallery-thumb:hover {
            opacity: 0.98;
            transform: translateY(-1px);
            border-color: rgba(223,194,120,0.7);
        }
        .art-gallery-thumb.active {
            opacity: 1;
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(200,168,75,0.25);
        }
        .art-3d-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            position: absolute; inset: 0;
            pointer-events: none;
        }
        .art-3d-placeholder.hidden { display: none; }
        .art-3d-icon {
            width: 80px; height: 80px;
            border: 1px solid rgba(200,168,75,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .art-3d-icon svg { width: 38px; height: 38px; stroke: rgba(200,168,75,0.35); fill: none; stroke-width: 1; }
        .art-3d-label { font-family: var(--fh); font-size: 15px; color: rgba(200,168,75,0.25); letter-spacing: 0.15em; text-transform: uppercase; }

        /* Description panel */
        .art-desc-panel {
            background: linear-gradient(180deg, rgba(12,28,18,0.98), rgba(10,24,15,0.98));
            display: flex;
            flex-direction: column;
            padding: 18px 18px 16px;
            gap: 14px;
            border-radius: 14px;
            border: 1px solid rgba(200,168,75,0.24);
            box-shadow: 0 10px 28px rgba(0,0,0,0.3);
        }
        .art-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--gold);
            border: 1px solid rgba(200,168,75,0.25);
            padding: 5px 12px;
            border-radius: 2px;
            background: rgba(200,168,75,0.06);
            width: fit-content;
        }
        .art-badge-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 6px var(--gold);
        }
        .art-divider {
            width: 40px; height: 1px;
            background: linear-gradient(90deg, var(--gold), transparent);
        }
        .art-divider-h {
            width: 40px; height: 1px;
            background: linear-gradient(90deg, var(--gold), transparent);
        }
        .art-title {
            font-size: clamp(24px, 2.6vw, 38px);
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
        }
        .art-title.ar-text { font-family: var(--fa); direction: rtl; text-align: right; }
        .art-title.ltr-text { font-family: var(--fh); letter-spacing: 0.02em; direction: ltr; text-align: left; }
        .art-title.ru-text { font-family: var(--ff); font-style: italic; direction: ltr; text-align: left; }

        .art-desc-sep {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(200,168,75,0.3);
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }
        .art-desc-sep::before, .art-desc-sep::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(200,168,75,0.1);
        }

        .art-text {
            font-size: 15px;
            line-height: 1.95;
            color: rgba(255,255,255,0.68);
        }
        .art-text.ar-text { font-family: var(--fa); direction: rtl; text-align: right; font-size: 16px; }
        .art-text.ltr-text { font-family: var(--ff); font-style: italic; direction: ltr; text-align: left; }
        .art-text.ru-text  { font-family: var(--ff); direction: ltr; text-align: left; }

        .art-meta-wrap {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(200,168,75,0.22);
            overflow: hidden;
            background: linear-gradient(180deg, rgba(10,26,14,0.65), rgba(7,14,9,0.85));
            box-shadow: 0 10px 28px rgba(0,0,0,0.28);
        }

        .art-meta {
            border: none;
            border-radius: 0;
            overflow: hidden;
            background: transparent;
        }
        .art-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-bottom: 1px solid rgba(200,168,75,0.07);
        }
        .art-meta-row:last-child { border-bottom: none; }
        .art-meta-key {
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.4);
            white-space: nowrap;
        }
        .art-meta-val {
            font-family: var(--ff);
            font-size: 13px; font-style: italic;
            color: rgba(255,255,255,0.4);
            text-align: right;
        }
        .art-category {
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.5);
        }

        .art-gallery-panel {
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 14px;
            padding: 10px;
            background: linear-gradient(180deg, rgba(12,27,17,0.92), rgba(8,20,13,0.95));
            box-shadow: 0 10px 28px rgba(0,0,0,0.28);
        }

        @media (max-width: 820px) {
            .art-main-split {
                grid-template-columns: 1fr;
            }
            .art-viewer {
                min-height: min(44vh, 400px);
            }
            .art-col-desc .art-desc-panel {
                min-height: 0;
            }
        }

        @media (max-width: 900px) {
            .art-body { padding: 12px 10px 18px; gap: 12px; }
            .art-head { padding: 12px 12px; }
            .art-date-line { font-size: 9px; letter-spacing: 0.12em; }
            .art-desc-panel { padding: 14px 12px 12px; }
            .art-bar { padding: 0 14px; gap: 8px; }
            .art-back, .art-narrate { font-size: 10px; padding: 6px 10px; }
            .art-main-image { width: min(86%, 420px); max-height: 78%; }
            .art-gallery-thumb { width: 78px; height: 58px; }
        }
    </style>
</head>
<body>

{{-- ────────────────────────────────────────────
     HEADER
──────────────────────────────────────────── --}}
<header class="hdr">

    {{-- Gauche --}}
    <div class="hdr-block l">
        <div class="emblem">
            {{-- resources/images/anp.png --}}
            <img src="{{ asset('images/anp.png') }}" alt="ANP"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <svg class="emblem-fallback" style="display:none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18" stroke-width="0.5"/>
            </svg>
        </div>
        <div class="hdr-txt">
            <div class="hdr-ar" lang="ar" dir="rtl">وزارة الدفاع الوطني</div>
            <div class="hdr-t1">Ministère de la</div>
            <div class="hdr-t2">Défense Nationale</div>
        </div>
    </div>

    <div class="hdr-site-lang" id="hdrSiteLang">
        <button class="hdr-lang-btn" id="hdrLangBtn" type="button" aria-haspopup="menu" aria-expanded="false">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M3 12h18M12 3a17 17 0 0 1 4 18M12 3a17 17 0 0 0-4 18"/>
            </svg>
            <span class="hdr-lang-code" id="hdrLangCode">FR</span>
            <span class="hdr-lang-chevron" aria-hidden="true">▾</span>
        </button>
        <div class="hdr-lang-sheet" id="hdrLangSheet" role="menu" aria-label="Language">
            <button type="button" class="hdr-lang-row active" data-site-lang="fr" role="menuitem">
                <span class="hdr-lang-row-flag">🇫🇷</span>
                <span class="hdr-lang-row-name">Français</span>
                <span class="hdr-lang-row-mini">FR</span>
            </button>
            <button type="button" class="hdr-lang-row" data-site-lang="ar" role="menuitem">
                <span class="hdr-lang-row-flag">🇩🇿</span>
                <span class="hdr-lang-row-name">العربية</span>
                <span class="hdr-lang-row-mini">AR</span>
            </button>
            <button type="button" class="hdr-lang-row" data-site-lang="en" role="menuitem">
                <span class="hdr-lang-row-flag">🇬🇧</span>
                <span class="hdr-lang-row-name">English</span>
                <span class="hdr-lang-row-mini">EN</span>
            </button>
            <button type="button" class="hdr-lang-row" data-site-lang="es" role="menuitem">
                <span class="hdr-lang-row-flag">🇪🇸</span>
                <span class="hdr-lang-row-name">Español</span>
                <span class="hdr-lang-row-mini">ES</span>
            </button>
            <button type="button" class="hdr-lang-row" data-site-lang="zh" role="menuitem">
                <span class="hdr-lang-row-flag">🇨🇳</span>
                <span class="hdr-lang-row-name">中文</span>
                <span class="hdr-lang-row-mini">ZH</span>
            </button>
            <button type="button" class="hdr-lang-row" data-site-lang="ru" role="menuitem">
                <span class="hdr-lang-row-flag">🇷🇺</span>
                <span class="hdr-lang-row-name">Русский</span>
                <span class="hdr-lang-row-mini">RU</span>
            </button>
        </div>
    </div>

    {{-- Droite --}}
    <div class="hdr-block r">
        <div class="emblem">
            {{-- resources/images/dic.png --}}
            <img src="{{ asset('images/dic.png') }}" alt="DIC"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            <svg class="emblem-fallback" style="display:none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18" stroke-width="0.5"/>
            </svg>
        </div>
        <div class="hdr-txt">
            <div class="hdr-ar" lang="ar" dir="rtl">مديرية الإعلام والاتصال</div>
            <div class="hdr-t1">Direction de l'Information</div>
            <div class="hdr-t2">et de la Communication</div>
        </div>
    </div>

</header>


{{-- ────────────────────────────────────────────
     HERO
──────────────────────────────────────────── --}}
<section class="hero" id="top">

    {{-- resources/images/bg.jpg --}}
    <div class="hero-bg"></div>
    <div class="hero-veil"></div>

    <div class="hero-content">

        <p class="hero-republic-line" data-site-i18n="hero_republic">République Algérienne Démocratique et Populaire</p>

        <div class="div-gold" aria-hidden="true">
            <div class="dg-line"></div>
            <div class="dg-diamond"></div>
            <div class="dg-line r"></div>
        </div>

        <h1 class="hero-title-line" data-site-i18n="hero_title">Musée de l'armée centrale</h1>

        <p class="hero-desc" data-site-i18n="hero_desc">
            Mémoire vivante de l'Armée Nationale Populaire —<br>
            un patrimoine d'honneur, de courage et d'histoire.
        </p>

        <div class="hero-btns">

            <button class="hbtn hbtn-gold" onclick="openQr()" type="button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 3h7v7H3zM5 5v3h3V5zM14 3h7v7h-7zM16 5v3h3V5zM3 14h7v7H3zM5 16v3h3v-3zM14 14h2v2h-2zM18 14h2v2h-2zM14 18h2v2h-2zM18 18h2v2h-2z"/>
                </svg>
                <span data-site-i18n="cta_scan">Scanner le QR</span>
            </button>

            <button class="hbtn hbtn-outline" onclick="goTo('sec-map')" type="button">
                <span data-site-i18n="cta_visit">Commencer la visite</span>
            </button>

            @auth
                @if(strtolower((string) auth()->user()->email) === strtolower((string) config('app.admin_email', 'admin@museum.local')))
                    <a class="hbtn hbtn-outline hbtn-admin" href="{{ url('/admin/artifacts') }}">
                        <span data-site-i18n="cta_admin">Administration</span>
                    </a>
                @endif
            @endauth

        </div>
    </div>

    <button class="hero-chevron" onclick="goTo('sec-org')" type="button" aria-label="Défiler">&#709;</button>

</section>


{{-- ────────────────────────────────────────────
     ORGANIGRAMME INSTITUTIONNEL (fond ivoire)
──────────────────────────────────────────── --}}
<section class="sec-org" id="sec-org">

    <div class="org-level">
        <p class="org-line" data-site-i18n="org_lvl1">Ministère de la Défense Nationale</p>
    </div>

    <div class="org-sep" aria-hidden="true">
        <div class="os-line"></div>
        <div class="os-diamond"></div>
        <div class="os-line"></div>
    </div>

    <div class="org-level">
        <p class="org-line" data-site-i18n="org_lvl2">État-major de l'Armée Nationale Populaire</p>
    </div>

    <div class="org-sep" aria-hidden="true">
        <div class="os-line"></div>
        <div class="os-diamond"></div>
        <div class="os-line"></div>
    </div>

    <div class="org-level">
        <p class="org-line" data-site-i18n="org_lvl3">Direction de l'Information et de la Communication</p>
    </div>

</section>


{{-- ────────────────────────────────────────────
     PLAN DU MUSÉE
──────────────────────────────────────────── --}}
<section class="sec-map" id="sec-map">
    <div class="map-wrap">
        <div class="map-head">
            <p class="map-headline" data-site-i18n="map_title">Plan du Musée Central de l'Armée</p>
        </div>

        <div class="org-sep" aria-hidden="true" style="margin-top:0; margin-bottom:30px;">
            <div class="os-line"></div>
            <div class="os-diamond"></div>
            <div class="os-line"></div>
        </div>

        <div class="map-grid">
            <div class="map-board" id="mapBoard" role="img" aria-label="Plan du parcours par niveau">
                <p class="map-title" id="mapBoardTitle">Parcours</p>
                <svg class="map-lines" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <path id="mapRoutePath" d="M20,72 L24,38 C30,22 46,20 54,32 C60,44 50,54 64,60 C74,64 80,48 86,42" />
                </svg>
                <div class="map-points-layer" id="mapPointsLayer"></div>
            </div>

            <aside class="map-panel">
                <h3 data-site-i18n="levels_title">Niveaux</h3>
                <p class="map-panel-hint" data-site-i18n="levels_hint">Sélectionnez un étage : le schéma à gauche et les points d’intérêt se mettent à jour.</p>
                <div class="map-floor-btns">
                    <button type="button" class="map-floor-btn active" data-floor="rdc" onclick="selectMuseumFloor('rdc')">
                        <span class="map-floor-label" data-site-i18n="floor_rdc">RDC · Accueil & Expositions</span>
                    </button>
                    <button type="button" class="map-floor-btn" data-floor="e1" onclick="selectMuseumFloor('e1')">
                        <span class="map-floor-label" data-site-i18n="floor_e1">Étage 1 · Chronologie historique</span>
                    </button>
                    <button type="button" class="map-floor-btn" data-floor="e2" onclick="selectMuseumFloor('e2')">
                        <span class="map-floor-label" data-site-i18n="floor_e2">Étage 2 · Collections militaires</span>
                    </button>
                    <button type="button" class="map-floor-btn" data-floor="e3" onclick="selectMuseumFloor('e3')">
                        <span class="map-floor-label" data-site-i18n="floor_e3">Étage 3 · Archives & mémoire</span>
                    </button>
                </div>
                <p class="map-note" id="mapFloorNote">Parcours indicatif — les QR des salles complètent la visite.</p>
            </aside>
        </div>
    </div>
</section>


{{-- ────────────────────────────────────────────
     FOOTER
──────────────────────────────────────────── --}}
<footer class="ftr">

    <div class="ftr-deco" aria-hidden="true">
        <div class="fd-line l"></div>
        <div class="fd-diamond"></div>
        <div class="fd-line r"></div>
    </div>

    <p class="ftr-title-line" data-site-i18n="footer_title">Musée de l'armée centrale</p>

    <p class="ftr-copy"><span class="ftr-cr">© {{ date('Y') }}</span> · <span data-site-i18n="footer_rights">ANP — Direction de l'Information et de la Communication</span></p>

</footer>


{{-- ────────────────────────────────────────────
     MODAL QR SCANNER
──────────────────────────────────────────── --}}
<div class="qr-ov" id="qrOverlay" onclick="bgClose(event)"
     role="dialog" aria-modal="true" aria-labelledby="qrTitle">

    <div class="qr-box">

        <div class="qr-top">
            <span class="qr-top-title" id="qrTitle" data-site-i18n="qr_title">Scanner un code QR</span>
            <button class="qr-x" onclick="closeQr()" type="button" aria-label="Fermer">✕</button>
        </div>

        <div class="qr-body">

            <div class="qr-stat">
                <div class="qr-dot" id="qrDot"></div>
                <span class="qr-stat-txt" id="qrMsg">En attente</span>
            </div>

            <div class="qr-vid-wrap">

                <div class="qr-idle" id="qrIdle">
                    <svg viewBox="0 0 64 64">
                        <rect x="4"  y="4"  width="24" height="24" rx="2" stroke-width="1.5"/>
                        <rect x="36" y="4"  width="24" height="24" rx="2" stroke-width="1.5"/>
                        <rect x="4"  y="36" width="24" height="24" rx="2" stroke-width="1.5"/>
                        <rect x="10" y="10" width="12" height="12" rx="1" stroke-width="1"/>
                        <rect x="42" y="10" width="12" height="12" rx="1" stroke-width="1"/>
                        <rect x="10" y="42" width="12" height="12" rx="1" stroke-width="1"/>
                        <path d="M40 40h6v6h-6zm8 0h6v6h-6zm-8 8h6v6h-6zm8 8h6v6h-6z" stroke-width="0.8"/>
                    </svg>
                    <p id="qrIdleTxt" data-site-i18n="qr_idle_hint">Appuyez sur « Activer la caméra »<br>pour scanner un objet du musée</p>
                </div>

                <video id="qrVideo" autoplay playsinline muted></video>

                <div class="qr-frame" id="qrFrame">
                    <div class="qfc tl"></div>
                    <div class="qfc tr"></div>
                    <div class="qfc bl"></div>
                    <div class="qfc br"></div>
                    <div class="qr-beam"></div>
                </div>

            </div>

            <div class="qr-res" id="qrRes">
                <div class="qr-res-lbl" id="qrResLbl" data-site-i18n="qr_code_label">Code détecté</div>
                <div class="qr-res-val" id="qrResVal">—</div>
            </div>

            <button id="qrBtn" onclick="startCam()" type="button" data-site-i18n="qr_activate">Activer la caméra</button>

            <div class="qr-alt">
                <div class="qr-alt-lbl" data-site-i18n="qr_alternative">Autres moyens</div>
                <div class="qr-alt-row">
                    <input type="text" id="qrManualId" placeholder="ex. artifact-001" autocomplete="off" autocapitalize="off">
                    <button type="button" class="qr-alt-btn primary" onclick="tryManualQr()" data-site-i18n="qr_open">Ouvrir</button>
                </div>
                <div class="qr-alt-row">
                    <input type="file" id="qrFileInput" accept="image/*" onchange="onQrImageSelected(event)">
                    <button type="button" class="qr-alt-btn" onclick="document.getElementById('qrFileInput').click()" data-site-i18n="qr_import">Importer une image QR</button>
                </div>
            </div>

            <p class="qr-note" data-site-i18n="qr_note">Caméra ou image : le code doit correspondre à l’identifiant enregistré (ex. artifact-001).</p>

        </div>
    </div>
</div>


{{-- ────────────────────────────────────────────
     ARTIFACT DETAIL OVERLAY
──────────────────────────────────────────── --}}
<div class="art-ov" id="artOverlay" role="dialog" aria-modal="true" aria-labelledby="artTitle">

    {{-- Top bar --}}
    <div class="art-bar">

        <button class="art-back" onclick="closeArtifact()" type="button">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Retour
        </button>

        <div class="art-lang-group" id="langGroup">
            <button class="lang-btn active" onclick="setLang('ar')"  data-lang="ar">العربية</button>
            <button class="lang-btn"        onclick="setLang('es')"  data-lang="es">Español</button>
            <button class="lang-btn"        onclick="setLang('fr')"  data-lang="fr">Français</button>
            <button class="lang-btn"        onclick="setLang('en')"  data-lang="en">English</button>
            <button class="lang-btn"        onclick="setLang('zh')"  data-lang="zh">中文</button>
            <button class="lang-btn"        onclick="setLang('ru')"  data-lang="ru">Русский</button>
        </div>

        <button class="art-narrate" id="narrateBtn" onclick="toggleNarrate()" type="button">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <path d="M11 5L6 9H2v6h4l5 4V5z"/>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
            </svg>
            <span id="narrateBtnTxt">Narration</span>
        </button>

    </div>

    {{-- Body --}}
    <div class="art-body">
        <div class="art-head">
            <div class="art-head-top">
                <div class="art-category" id="artCategory">— Objet de musée —</div>
                <div class="art-date-line" id="artEpoque">—</div>
            </div>
            <h2 class="art-title ar-text" id="artTitle">…</h2>
        </div>

        <div class="art-main-split">
            <div class="art-col-visual">
                {{-- 3D Viewer --}}
                <div class="art-viewer">
                    <div class="art-image-stage" id="artImageStage" hidden>
                        <img id="artMainImage" class="art-main-image" src="" alt="Aperçu artefact">
                    </div>
                    <model-viewer
                        id="artModelViewer"
                        camera-controls
                        auto-rotate
                        ar
                        shadow-intensity="1"
                        style="width:100%;height:100%;background:transparent;"
                    ></model-viewer>

                    {{-- Placeholder shown when no GLB is loaded --}}
                    <div class="art-3d-placeholder" id="art3dPlaceholder">
                        <div class="art-3d-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <path d="M12 22V12M3.27 6.96 12 12l8.73-5.04"/>
                            </svg>
                        </div>
                        <div class="art-3d-label">Modélisation 3D</div>
                        <div class="art-3d-sub">نموذج ثلاثي الأبعاد</div>
                    </div>
                </div>
            </div>

            <div class="art-col-desc">
                {{-- Description (language via top bar pills) --}}
                <div class="art-desc-panel">
                    <p class="art-text ar-text" id="artDesc">…</p>
                </div>
            </div>
        </div>

        <div class="art-meta-wrap">
            <div class="art-meta" id="artMeta">
                <div class="art-meta-row">
                    <span class="art-meta-key">Réf.</span>
                    <span class="art-meta-val" id="artRef">—</span>
                </div>
                <div class="art-meta-row">
                    <span class="art-meta-key">Section</span>
                    <span class="art-meta-val" id="artSection">—</span>
                </div>
            </div>
        </div>

        <div class="art-gallery-panel">
            <div class="art-gallery-strip" id="artGallery"></div>
        </div>
    </div>
</div>


{{-- ────────────────────────────────────────────
     JAVASCRIPT
──────────────────────────────────────────── --}}
<script>
/* Base lookup artefact — url() prend en charge sous-dossier / APP_URL */
var ARTIFACT_LOOKUP_BASE = @json(rtrim(url('/api/artifacts/by-qr'), '/'));

var SCROLL_Y = 0;

function setViewportHeight() {
    var vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', vh + 'px');
}

setViewportHeight();
window.addEventListener('resize', setViewportHeight, { passive: true });
window.addEventListener('orientationchange', setViewportHeight, { passive: true });

/* ── Scroll ────────────────────────────── */
function goTo(id) {
    var el = document.getElementById(id);
    var header = document.querySelector('.hdr');
    var offset = header ? header.offsetHeight + 4 : 68;
    if (el) window.scrollTo({ top: el.getBoundingClientRect().top + scrollY - offset, behavior:'smooth' });
}

/* ── Home page language switcher ───────────── */
var SITE_LANG = 'fr';
var SITE_LANG_META = {
    fr: { label: 'Français', code: 'FR' },
    ar: { label: 'العربية', code: 'AR' },
    en: { label: 'English',  code: 'EN' },
    es: { label: 'Español',  code: 'ES' },
    zh: { label: '中文',      code: 'ZH' },
    ru: { label: 'Русский',  code: 'RU' }
};

var SITE_LANG_HTML = {
    fr: 'fr',
    ar: 'ar',
    en: 'en',
    es: 'es',
    zh: 'zh-CN',
    ru: 'ru'
};

var CURRENT_MAP_FLOOR = 'rdc';

var SITE_I18N = {
    fr: {
        hero_republic: 'République Algérienne Démocratique et Populaire',
        hero_title: 'Musée de l\'armée centrale',
        hero_desc: 'Mémoire vivante de l\'Armée Nationale Populaire —<br>un patrimoine d\'honneur, de courage et d\'histoire.',
        cta_scan: 'Scanner le QR',
        cta_visit: 'Commencer la visite',
        cta_admin: 'Administration',
        map_title: 'Plan du Musée Central de l\'Armée',
        levels_title: 'Niveaux',
        levels_hint: 'Sélectionnez un étage : le schéma à gauche et les points d’intérêt se mettent à jour.',
        floor_rdc: 'RDC · Accueil & Expositions',
        floor_e1: 'Étage 1 · Chronologie historique',
        floor_e2: 'Étage 2 · Collections militaires',
        floor_e3: 'Étage 3 · Archives & mémoire',
        org_lvl1: 'Ministère de la Défense Nationale',
        org_lvl2: 'État-major de l\'Armée Nationale Populaire',
        org_lvl3: 'Direction de l\'Information et de la Communication',
        footer_title: 'Musée de l\'armée centrale',
        footer_rights: 'ANP — Direction de l\'Information et de la Communication',
        qr_title: 'Scanner un code QR',
        qr_activate: 'Activer la caméra',
        qr_alternative: 'Autres moyens',
        qr_open: 'Ouvrir',
        qr_import: 'Importer une image QR',
        qr_note: 'Caméra ou image : le code doit correspondre à l’identifiant enregistré (ex. artifact-001).',
        qr_idle_hint: 'Appuyez sur « Activer la caméra »<br>pour scanner un objet du musée',
        qr_code_label: 'Code détecté',
        qr_status_idle: 'En attente'
    },
    ar: {
        hero_republic: 'الجمهورية الجزائرية الديمقراطية الشعبية',
        hero_title: 'المتحف المركزي للجيش',
        hero_desc: 'ذاكرة حيّة للجيش الوطني الشعبي —<br>تراث من الشرف والشجاعة والتاريخ.',
        cta_scan: 'مسح رمز QR',
        cta_visit: 'ابدأ الزيارة',
        cta_admin: 'الإدارة',
        map_title: 'خريطة المتحف المركزي للجيش',
        levels_title: 'الطوابق',
        levels_hint: 'اختر طابقا: سيتم تحديث المخطط ونقاط الاهتمام.',
        floor_rdc: 'الطابق الأرضي · الاستقبال والمعارض',
        floor_e1: 'الطابق 1 · التسلسل التاريخي',
        floor_e2: 'الطابق 2 · المجموعات العسكرية',
        floor_e3: 'الطابق 3 · الأرشيف والذاكرة',
        org_lvl1: 'وزارة الدفاع الوطني',
        org_lvl2: 'أركان الجيش الوطني الشعبي',
        org_lvl3: 'مديرية الإعلام والاتصال',
        footer_title: 'المتحف المركزي للجيش',
        footer_rights: 'الجيش الوطني الشعبي — مديرية الإعلام والاتصال',
        qr_title: 'مسح رمز QR',
        qr_activate: 'تفعيل الكاميرا',
        qr_alternative: 'خيارات أخرى',
        qr_open: 'فتح',
        qr_import: 'استيراد صورة QR',
        qr_note: 'باستخدام الكاميرا أو الصورة: يجب أن يطابق الرمز المعرّف المسجل (مثال: artifact-001).',
        qr_idle_hint: 'اضغط « تفعيل الكاميرا »<br>لمسح قطعة في المتحف',
        qr_code_label: 'الرمز المكتشف',
        qr_status_idle: 'في الانتظار'
    },
    en: {
        hero_republic: 'People\'s Democratic Republic of Algeria',
        hero_title: 'Central Army Museum',
        hero_desc: 'A living memory of the National People\'s Army —<br>a heritage of honor, courage, and history.',
        cta_scan: 'Scan QR',
        cta_visit: 'Start the tour',
        cta_admin: 'Administration',
        map_title: 'Central Army Museum Map',
        levels_title: 'Levels',
        levels_hint: 'Select a floor: the diagram and points of interest update instantly.',
        floor_rdc: 'Ground Floor · Reception & Exhibitions',
        floor_e1: 'Level 1 · Historical Timeline',
        floor_e2: 'Level 2 · Military Collections',
        floor_e3: 'Level 3 · Archives & Memory',
        org_lvl1: 'Ministry of National Defence',
        org_lvl2: 'General Staff of the National People\'s Army',
        org_lvl3: 'Directorate of Information and Communication',
        footer_title: 'Central Army Museum',
        footer_rights: 'NPA — Directorate of Information and Communication',
        qr_title: 'Scan a QR code',
        qr_activate: 'Enable camera',
        qr_alternative: 'Alternative methods',
        qr_open: 'Open',
        qr_import: 'Import QR image',
        qr_note: 'Camera or image: the code must match a registered identifier (e.g. artifact-001).',
        qr_idle_hint: 'Tap « Enable camera »<br>to scan a museum artifact',
        qr_code_label: 'Code detected',
        qr_status_idle: 'Idle'
    },
    es: {
        hero_republic: 'República Argelina Democrática y Popular',
        hero_title: 'Museo Central del Ejército',
        hero_desc: 'Memoria viva del Ejército Nacional Popular —<br>un patrimonio de honor, valentía e historia.',
        cta_scan: 'Escanear QR',
        cta_visit: 'Comenzar la visita',
        cta_admin: 'Administración',
        map_title: 'Plano del Museo Central del Ejército',
        levels_title: 'Niveles',
        levels_hint: 'Seleccione una planta: el esquema y los puntos de interés se actualizan.',
        floor_rdc: 'Planta baja · Recepción y exposiciones',
        floor_e1: 'Planta 1 · Cronología histórica',
        floor_e2: 'Planta 2 · Colecciones militares',
        floor_e3: 'Planta 3 · Archivos y memoria',
        org_lvl1: 'Ministerio de Defensa Nacional',
        org_lvl2: 'Estado Mayor del Ejército Nacional Popular',
        org_lvl3: 'Dirección de Información y Comunicación',
        footer_title: 'Museo Central del Ejército',
        footer_rights: 'ENP — Dirección de Información y Comunicación',
        qr_title: 'Escanear un código QR',
        qr_activate: 'Activar cámara',
        qr_alternative: 'Otros métodos',
        qr_open: 'Abrir',
        qr_import: 'Importar imagen QR',
        qr_note: 'Cámara o imagen: el código debe coincidir con un identificador registrado (ej. artifact-001).',
        qr_idle_hint: 'Pulse « Activar cámara »<br>para escanear una pieza del museo',
        qr_code_label: 'Código detectado',
        qr_status_idle: 'En espera'
    },
    zh: {
        hero_republic: '阿尔及利亚民主人民共和国',
        hero_title: '中央军事博物馆',
        hero_desc: '人民军队的鲜活记忆 —<br>荣誉、勇气与历史的传承。',
        cta_scan: '扫描二维码',
        cta_visit: '开始参观',
        cta_admin: '管理',
        map_title: '中央军事博物馆导览图',
        levels_title: '楼层',
        levels_hint: '选择楼层后，左侧路线和兴趣点将自动更新。',
        floor_rdc: '一层 · 接待与展览',
        floor_e1: '二层 · 历史时间线',
        floor_e2: '三层 · 军事藏品',
        floor_e3: '四层 · 档案与记忆',
        org_lvl1: '国防部',
        org_lvl2: '人民军总参谋部',
        org_lvl3: '新闻与通讯司',
        footer_title: '中央军事博物馆',
        footer_rights: '人民军 — 新闻与通讯司',
        qr_title: '扫描二维码',
        qr_activate: '启用摄像头',
        qr_alternative: '其他方式',
        qr_open: '打开',
        qr_import: '导入二维码图片',
        qr_note: '使用摄像头或图片：二维码需匹配已登记的标识（如 artifact-001）。',
        qr_idle_hint: '点击「启用摄像头」<br>扫描馆内展品',
        qr_code_label: '已识别代码',
        qr_status_idle: '待命'
    },
    ru: {
        hero_republic: 'Алжирская Народная Демократическая Республика',
        hero_title: 'Центральный музей армии',
        hero_desc: 'Живая память Национальной народной армии —<br>наследие чести, мужества и истории.',
        cta_scan: 'Сканировать QR',
        cta_visit: 'Начать посещение',
        cta_admin: 'Администрирование',
        map_title: 'Карта Центрального музея армии',
        levels_title: 'Этажи',
        levels_hint: 'Выберите этаж: схема и точки интереса обновятся автоматически.',
        floor_rdc: '1-й этаж · Прием и экспозиции',
        floor_e1: '2-й этаж · Историческая хронология',
        floor_e2: '3-й этаж · Военные коллекции',
        floor_e3: '4-й этаж · Архивы и память',
        org_lvl1: 'Министерство национальной обороны',
        org_lvl2: 'Генеральный штаб Народной национальной армии',
        org_lvl3: 'Управление информации и связи',
        footer_title: 'Центральный музей армии',
        footer_rights: 'ННА — Управление информации и связи',
        qr_title: 'Сканировать QR-код',
        qr_activate: 'Включить камеру',
        qr_alternative: 'Другие способы',
        qr_open: 'Открыть',
        qr_import: 'Импортировать QR-изображение',
        qr_note: 'Камера или изображение: код должен соответствовать зарегистрированному идентификатору (например, artifact-001).',
        qr_idle_hint: 'Нажмите « Включить камеру », <br>чтобы отсканировать экспонат',
        qr_code_label: 'Код обнаружен',
        qr_status_idle: 'Ожидание'
    }
};

function qrStatusIdleMsg() {
    var dict = SITE_I18N[SITE_LANG] || SITE_I18N.fr;
    return dict.qr_status_idle || '…';
}

/** Données brutes d’un objet locale de l’API (sans chaînage fallback) */
function normalizeArtifactBlock(raw) {
    if (!raw || typeof raw !== 'object') return null;
    var title = raw.title != null ? String(raw.title) : '';
    var desc = raw.desc != null ? String(raw.desc) : '';
    if (!desc.trim() && raw.description != null) desc = String(raw.description);
    return { title: title, desc: desc };
}

/** Bloc { title, desc } pour affichage : langue choisie, sinon en → fr */
function getArtifactLocale(lang) {
    if (!CURRENT_ARTIFACT || !lang) return null;

    var primary = normalizeArtifactBlock(CURRENT_ARTIFACT[lang]);
    if (primary && (primary.title.trim() || primary.desc.trim())) return primary;

    var order = lang === 'en' ? ['fr'] : ['en', 'fr', 'ar', 'es', 'zh', 'ru'];
    var i;
    for (i = 0; i < order.length; i++) {
        if (order[i] === lang) continue;
        var fb = normalizeArtifactBlock(CURRENT_ARTIFACT[order[i]]);
        if (fb && (fb.title.trim() || fb.desc.trim())) return fb;
    }
    return primary || normalizeArtifactBlock(CURRENT_ARTIFACT.fr) || { title: '', desc: '' };
}

function localeHasContent(lang) {
    var b = normalizeArtifactBlock(CURRENT_ARTIFACT ? CURRENT_ARTIFACT[lang] : null);
    return !!(b && (b.title.trim() || b.desc.trim()));
}

function pickArtifactLangCode() {
    if (!CURRENT_ARTIFACT) return SITE_LANG || 'fr';
    if (localeHasContent(SITE_LANG)) return SITE_LANG;
    var order = ['fr', 'ar', 'en', 'es', 'zh', 'ru'];
    var i;
    for (i = 0; i < order.length; i++) {
        var c = order[i];
        if (localeHasContent(c)) return c;
    }
    return 'fr';
}

function refreshMuseumMapForLang() {
    if (typeof selectMuseumFloor === 'function') {
        selectMuseumFloor(CURRENT_MAP_FLOOR);
    }
}

function setSiteLang(lang) {
    var dict = SITE_I18N[lang];
    if (!dict) lang = 'fr';
    SITE_LANG = lang;
    dict = SITE_I18N[SITE_LANG];

    document.querySelectorAll('[data-site-i18n]').forEach(function(el) {
        var key = el.getAttribute('data-site-i18n');
        if (!key || !dict[key]) return;
        el.innerHTML = dict[key];
    });

    document.body.classList.toggle('site-lang-ar', SITE_LANG === 'ar');
    document.documentElement.lang = SITE_LANG_HTML[SITE_LANG] || 'fr';

    try {
        localStorage.setItem('musescan_site_lang', SITE_LANG);
    } catch (err) { /* ignore */ }

    var codeEl = document.getElementById('hdrLangCode');
    var meta = SITE_LANG_META[SITE_LANG] || SITE_LANG_META.fr;
    if (codeEl) codeEl.textContent = meta.code;

    document.querySelectorAll('.hdr-lang-row[data-site-lang]').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-site-lang') === SITE_LANG);
    });

    if (typeof CURRENT_ARTIFACT !== 'undefined' && CURRENT_ARTIFACT) {
        CURRENT_LANG = pickArtifactLangCode();
        _applyLang(CURRENT_LANG);
    }

    refreshMuseumMapForLang();

    var qrDot = document.getElementById('qrDot');
    var qrMsg = document.getElementById('qrMsg');
    if (qrDot && qrMsg && qrDot.className === 'qr-dot' && !qrDot.classList.contains('scanning') && !qrDot.classList.contains('ok') && !qrDot.classList.contains('err')) {
        qrMsg.textContent = qrStatusIdleMsg();
    }
}

function initHdrSiteLang() {
    var wrap = document.getElementById('hdrSiteLang');
    var toggle = document.getElementById('hdrLangBtn');
    var sheet = document.getElementById('hdrLangSheet');
    if (!wrap || !toggle || !sheet) return;

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        var opened = wrap.classList.toggle('open');
        toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
    });

    sheet.querySelectorAll('[data-site-lang]').forEach(function(btn) {
        btn.addEventListener('click', function(ev) {
            ev.stopPropagation();
            setSiteLang(btn.getAttribute('data-site-lang'));
            wrap.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', function(ev) {
        if (!wrap.contains(ev.target)) {
            wrap.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    var initial = 'fr';
    try {
        var st = localStorage.getItem('musescan_site_lang');
        if (st && SITE_I18N[st]) initial = st;
    } catch (e2) { /* ignore */ }
    setSiteLang(initial);
}

document.addEventListener('DOMContentLoaded', initHdrSiteLang);

/* ── Modal ─────────────────────────────── */
function openQr() {
    document.getElementById('qrOverlay').classList.add('open');
    SCROLL_Y = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = '-' + SCROLL_Y + 'px';
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
}

function closeQr() {
    document.getElementById('qrOverlay').classList.remove('open');
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    window.scrollTo(0, SCROLL_Y || 0);
    stopCam();
    resetUi();
}

function bgClose(e) {
    if (e.target === document.getElementById('qrOverlay')) closeQr();
}

document.addEventListener('keydown', function(e){ if (e.key==='Escape') closeQr(); });
document.addEventListener('visibilitychange', function(){
    if (document.hidden && CAM) stopCam();
});

/* ── Camera & jsQR ─────────────────────────────────────────
   DETACHED canvas (never in DOM) → overflow:hidden on any
   parent element can NEVER block ctx.drawImage().
   setInterval (not rAF) → keeps scanning when tab loses focus.
──────────────────────────────────────────────────────────── */
var CAM         = null;
var SCAN_TIMER  = null;   /* setInterval handle              */
var ACTIVE      = false;
var LAST        = null;
var SCAN_N      = 0;      /* frame counter for full-res passes */

/* Create a detached canvas once and reuse it */
var SCAN_CANVAS = document.createElement('canvas');
var SCAN_CTX    = SCAN_CANVAS.getContext('2d', { willReadFrequently: true });

function getCameraStream() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        return Promise.reject(new Error('NO_MEDIA'));
    }
    var tries = [
        {
            video: {
                facingMode: { ideal: 'environment' },
                width:  { ideal: 1920 },
                height: { ideal: 1080 }
            },
            audio: false
        },
        {
            video: {
                facingMode: { ideal: 'environment' },
                width:  { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        },
        { video: { facingMode: { ideal: 'environment' } }, audio: false },
        { video: true, audio: false }
    ];
    function attempt(i) {
        if (i >= tries.length) return Promise.reject(new Error('CAMERA_FAIL'));
        return navigator.mediaDevices.getUserMedia(tries[i]).catch(function() {
            return attempt(i + 1);
        });
    }
    return attempt(0);
}

function startCam() {
    var btn = document.getElementById('qrBtn');
    btn.disabled = true;
    setStatus('wait', 'Demande d\'accès en cours…');

    getCameraStream()
    .then(function(stream) {
        CAM    = stream;
        ACTIVE = true;
        SCAN_N = 0;

        var v = document.getElementById('qrVideo');
        v.srcObject = stream;
        v.style.display = 'block';

        document.getElementById('qrIdle').style.display  = 'none';
        document.getElementById('qrFrame').style.display = 'block';
        document.getElementById('qrBtn').style.display   = 'none';

        setStatus('scanning', 'Caméra active — placez le QR bien net et lumineux');

        function beginScan() {
            if (SCAN_TIMER) return; /* guard: start only once */
            SCAN_TIMER = setInterval(scanFrame, 100);
        }

        /* All three events + hard timeout to cover every browser */
        v.addEventListener('loadeddata',     beginScan, { once: true });
        v.addEventListener('loadedmetadata', beginScan, { once: true });
        v.addEventListener('canplay',        beginScan, { once: true });

        v.play().catch(function() {});
        setTimeout(function() { if (ACTIVE && !SCAN_TIMER) beginScan(); }, 900);
    })
    .catch(function(err) {
        btn.disabled = false;
        if (err && err.message === 'NO_MEDIA') {
            setStatus('err', 'Caméra non supportée');
            return;
        }
        var m = {
            'NotAllowedError'     : 'Permission refusée — autorisez la caméra dans les paramètres',
            'NotFoundError'       : 'Aucune caméra détectée',
            'NotReadableError'    : 'Caméra occupée par une autre application',
            'OverconstrainedError': 'Contrainte vidéo non supportée'
        };
        setStatus('err', m[err.name] || 'Erreur caméra : ' + (err.name || err.message));
    });
}

/* Called every 150 ms by setInterval */
function scanFrame() {
    if (!ACTIVE) { clearInterval(SCAN_TIMER); SCAN_TIMER = null; return; }

    var v = document.getElementById('qrVideo');
    if (!v || v.readyState < 2 || v.videoWidth === 0 || v.videoHeight === 0) return;

    SCAN_N += 1;
    /* Every 4th frame: full resolution decode (helps phone-screen / moiré) */
    var useFull = (SCAN_N % 4 === 0);
    var maxW = useFull ? 2048 : 1600;
    var scale = Math.min(1, maxW / v.videoWidth);
    var cw    = Math.round(v.videoWidth  * scale);
    var ch    = Math.round(v.videoHeight * scale);

    if (SCAN_CANVAS.width !== cw)  SCAN_CANVAS.width  = cw;
    if (SCAN_CANVAS.height !== ch) SCAN_CANVAS.height = ch;

    try {
        SCAN_CTX.drawImage(v, 0, 0, cw, ch);
        var imgData = SCAN_CTX.getImageData(0, 0, cw, ch);

        if (typeof jsQR === 'undefined') return; /* library not loaded yet */

        /* attemptBoth: handles dark-on-light AND light-on-dark QR codes */
        var code = jsQR(imgData.data, imgData.width, imgData.height, {
            inversionAttempts: 'attemptBoth'
        });

        if (code && code.data && code.data !== LAST) {
            LAST = code.data;
            onDetected(normalizeQrPayload(code.data));
        }
    } catch(e) { /* ignore single-frame decode errors */ }
}

/* Trim, extra path segments, full URLs → artifact id */
function normalizeQrPayload(raw) {
    var s = String(raw || '').trim();
    if (!s) return s;
    try {
        var u = new URL(s);
        var parts = u.pathname.split('/').filter(Boolean);
        var last = parts[parts.length - 1];
        if (last) return decodeURIComponent(last);
    } catch (e) { /* not a URL */ }
    return s;
}

function tryManualQr() {
    var el = document.getElementById('qrManualId');
    var v = normalizeQrPayload(el ? el.value : '');
    if (!v) {
        setStatus('err', 'Entrez un identifiant (ex. artifact-001)');
        return;
    }
    onDetected(v);
}

function onQrImageSelected(ev) {
    var f = ev.target && ev.target.files && ev.target.files[0];
    ev.target.value = '';
    if (!f || typeof jsQR === 'undefined') return;
    setStatus('wait', 'Lecture de l’image…');
    var reader = new FileReader();
    reader.onload = function() {
        var img = new Image();
        img.onload = function() {
            try {
                var maxSide = 2048;
                var w = img.naturalWidth;
                var h = img.naturalHeight;
                var sc = (w > maxSide || h > maxSide) ? Math.min(maxSide / w, maxSide / h) : 1;
                var cw = Math.round(w * sc);
                var ch = Math.round(h * sc);
                if (SCAN_CANVAS.width !== cw) SCAN_CANVAS.width = cw;
                if (SCAN_CANVAS.height !== ch) SCAN_CANVAS.height = ch;
                SCAN_CTX.drawImage(img, 0, 0, cw, ch);
                var imgData = SCAN_CTX.getImageData(0, 0, cw, ch);
                var code = jsQR(imgData.data, imgData.width, imgData.height, {
                    inversionAttempts: 'attemptBoth'
                });
                if (code && code.data) {
                    setStatus('ok', 'QR lu depuis l’image');
                    onDetected(normalizeQrPayload(code.data));
                } else {
                    setStatus('err', 'Aucun QR détecté sur cette image');
                }
            } catch (e) {
                setStatus('err', 'Impossible de lire l’image');
            }
        };
        img.onerror = function() { setStatus('err', 'Image invalide'); };
        img.src = reader.result;
    };
    reader.onerror = function() { setStatus('err', 'Fichier illisible'); };
    reader.readAsDataURL(f);
}

/* QR non reconnu: contenu de secours */
var ARTIFACT_FALLBACK = {
    modelSrc : '',
    mainImage: '',
    galleryImages: [],
    category : 'Collection du musée · مجموعة المتحف',
    ref      : '—',
    epoque   : '—',
    section  : '—',
    ar: { title: 'قطعة أثرية', desc : 'هذه القطعة الأثرية جزء من المجموعة التاريخية للمتحف المركزي للجيش الوطني الشعبي. يرجى مراجعة أحد أعضاء الطاقم للحصول على مزيد من المعلومات.' },
    es: { title: 'Pieza de museo', desc : 'Esta pieza forma parte de la colección histórica del Museo Central del Ejército Nacional Popular. Consulte a un miembro del personal para obtener más información.' },
    fr: { title: 'Pièce de musée', desc : 'Cette pièce fait partie de la collection historique du Musée Central de l\'Armée Nationale Populaire. Veuillez vous adresser à un membre du personnel pour plus d\'informations.' },
    en: { title: 'Museum Artifact', desc : 'This item is part of the historical collection of the Central Museum of the National People\'s Army. Please consult a staff member for more information.' },
    zh: { title: '博物馆文物', desc : '该文物是人民军队中央博物馆历史馆藏的一部分。请向工作人员咨询更多信息。' },
    ru: { title: 'Музейный экспонат', desc : 'Этот экспонат является частью исторической коллекции Центрального музея Национальной народной армии. Обратитесь к сотруднику музея для получения дополнительной информации.' }
};

/* 变量 */
var CURRENT_ARTIFACT = null;
var CURRENT_LANG     = 'fr';
var CURRENT_GALLERY  = [];
var NARRATING        = false;

/* Code détecté — ouvre l'overlay artefact */
function onDetected(val) {
    val = normalizeQrPayload(val);
    ACTIVE = false;
    setStatus('ok', 'Code QR détecté !');
    document.getElementById('qrRes').style.display = 'block';
    document.getElementById('qrResVal').textContent = val;
    if (navigator.vibrate) navigator.vibrate(45);
    stopCam();
    closeQr();
    setTimeout(function(){ openArtifact(val); }, 220);
}

function rescan() {
    LAST   = null;
    ACTIVE = true;
    document.getElementById('qrRes').style.display   = 'none';
    document.getElementById('qrFrame').style.display = 'block';
    document.getElementById('qrBtn').style.display   = 'none';
    setStatus('scanning', 'Caméra active — Cherche un QR code…');
    if (!SCAN_TIMER) SCAN_TIMER = setInterval(scanFrame, 100);
}

/* ════════════════════════════════════════════════
   ARTIFACT OVERLAY
════════════════════════════════════════════════ */
async function openArtifact(qrVal) {
    var data = await fetchArtifactByQr(qrVal);
    CURRENT_ARTIFACT = data || ARTIFACT_FALLBACK;
    NARRATING        = false;

    /* 3D model */
    var mv = document.getElementById('artModelViewer');
    var ph = document.getElementById('art3dPlaceholder');
    if (CURRENT_ARTIFACT.modelSrc) {
        mv.setAttribute('src', CURRENT_ARTIFACT.modelSrc);
        ph.classList.add('hidden');
    } else {
        mv.removeAttribute('src');
        ph.classList.remove('hidden');
    }

    CURRENT_GALLERY = Array.isArray(CURRENT_ARTIFACT.galleryImages) ? CURRENT_ARTIFACT.galleryImages.slice() : [];
    if (CURRENT_ARTIFACT.mainImage && CURRENT_GALLERY.indexOf(CURRENT_ARTIFACT.mainImage) === -1) {
        CURRENT_GALLERY.unshift(CURRENT_ARTIFACT.mainImage);
    }
    setMainArtifactImage(CURRENT_ARTIFACT.mainImage || CURRENT_GALLERY[0] || '');
    renderArtifactGallery(CURRENT_GALLERY);

    /* Meta */
    document.getElementById('artCategory').textContent = CURRENT_ARTIFACT.category;
    document.getElementById('artRef').textContent      = CURRENT_ARTIFACT.ref;
    document.getElementById('artEpoque').textContent   = CURRENT_ARTIFACT.epoque;
    document.getElementById('artSection').textContent  = CURRENT_ARTIFACT.section;

    CURRENT_LANG = pickArtifactLangCode();
    _applyLang(CURRENT_LANG);

    /* Narration button reset */
    var nb = document.getElementById('narrateBtn');
    nb.classList.remove('speaking');
    document.getElementById('narrateBtnTxt').textContent = 'Narration';

    /* Show overlay */
    var ov = document.getElementById('artOverlay');
    ov.classList.add('open');
    document.body.style.overflow = 'hidden';
}

async function fetchArtifactByQr(qrVal) {
    try {
        var url = ARTIFACT_LOOKUP_BASE + '/' + encodeURIComponent(qrVal);
        var response = await fetch(url, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        });

        if (!response.ok) return ARTIFACT_FALLBACK;
        var data = await response.json();
        if (!data || typeof data !== 'object') return ARTIFACT_FALLBACK;
        return data;
    } catch (e) {
        return ARTIFACT_FALLBACK;
    }
}

function closeArtifact() {
    stopNarration();
    document.getElementById('artOverlay').classList.remove('open');
    document.body.style.overflow = '';
    setMainArtifactImage('');
    renderArtifactGallery([]);
    LAST = null; /* Allow rescanning same code */
}

function setMainArtifactImage(src) {
    var stage = document.getElementById('artImageStage');
    var img = document.getElementById('artMainImage');
    if (!stage || !img) return;

    if (!src) {
        img.src = '';
        stage.hidden = true;
        return;
    }

    img.src = src;
    stage.hidden = false;
}

function renderArtifactGallery(images) {
    var strip = document.getElementById('artGallery');
    if (!strip) return;

    strip.innerHTML = '';

    if (!Array.isArray(images) || images.length <= 1) return;

    images.forEach(function(src, idx) {
        var thumb = document.createElement('img');
        thumb.className = 'art-gallery-thumb' + (idx === 0 ? ' active' : '');
        thumb.src = src;
        thumb.alt = 'Vue artefact ' + (idx + 1);
        thumb.addEventListener('click', function() {
            setMainArtifactImage(src);
            strip.querySelectorAll('.art-gallery-thumb').forEach(function(el) {
                el.classList.remove('active');
            });
            thumb.classList.add('active');
        });
        strip.appendChild(thumb);
    });
}

function setLang(lang) {
    if (!CURRENT_ARTIFACT) return;
    CURRENT_LANG = lang;
    _applyLang(lang);
    /* If narrating, restart in new language */
    if (NARRATING) { stopNarration(); startNarration(); }
}

function _applyLang(lang) {
    if (!CURRENT_ARTIFACT) return;
    var d = getArtifactLocale(lang);
    if (!d) {
        d = getArtifactLocale('fr') || getArtifactLocale('en') || { title: '', desc: '' };
    }
    var isAr    = (lang === 'ar');
    var titleEl = document.getElementById('artTitle');
    var descEl  = document.getElementById('artDesc');

    titleEl.textContent = d.title;
    descEl.textContent  = d.desc;

    var titleCls = 'art-title ';
    var descCls  = 'art-text ';
    if (lang === 'ar') { titleCls += 'ar-text'; descCls += 'ar-text'; }
    else if (lang === 'zh') { titleCls += 'ltr-text'; descCls += 'ltr-text'; }
    else if (lang === 'ru') { titleCls += 'ru-text'; descCls += 'ru-text'; }
    else { titleCls += 'ltr-text'; descCls += 'ltr-text'; }

    titleEl.className = titleCls;
    descEl.className  = descCls;

    document.querySelectorAll('#artOverlay .lang-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });
}

/* ── Narration (Web Speech API) ── */
var LANG_BCP47 = {
    ar : 'ar-SA',
    es : 'es-ES',
    fr : 'fr-FR',
    en : 'en-US',
    zh : 'zh-CN',
    ru : 'ru-RU'
};

function toggleNarrate() {
    if (NARRATING) { stopNarration(); } else { startNarration(); }
}

function pickVoiceForLang(langKey, utterance, langBcp47) {
    utterance.lang = langBcp47;
    utterance.voice = null;
    var voices = window.speechSynthesis.getVoices();
    if (!voices || !voices.length) return;

    var want = LANG_BCP47[langKey] || langBcp47;
    var pref = want.split('-')[0].toLowerCase();
    var v;
    for (var i = 0; i < voices.length; i++) {
        var L = String(voices[i].lang || '').toLowerCase().replace('_', '-');
        if (L.indexOf(pref) === 0) {
            v = voices[i]; break;
        }
    }
    if (!v) {
        var code = LANG_BCP47[langKey] || '';
        for (var j = 0; j < voices.length; j++) {
            var L2 = String(voices[j].lang || '').toLowerCase();
            if (code && L2.indexOf(code.toLowerCase()) === 0) { v = voices[j]; break; }
        }
    }
    if (v) {
        utterance.voice = v;
        utterance.lang  = v.lang || utterance.lang;
    }
}

function startNarration() {
    if (!window.speechSynthesis || !CURRENT_ARTIFACT) return;

    var bundle = getArtifactLocale(CURRENT_LANG) || getArtifactLocale('fr') || getArtifactLocale('en');
    if (!bundle) return;
    var text = (String(bundle.title).trim() + '. ' + String(bundle.desc).trim()).trim();
    if (!text) return;

    var langFallback = LANG_BCP47[CURRENT_LANG] || 'fr-FR';
    window.speechSynthesis.cancel();

    var utt = new SpeechSynthesisUtterance(text);
    utt.rate = 0.92;

    utt.onend = utt.onerror = function() {
        NARRATING = false;
        var nb = document.getElementById('narrateBtn');
        if (nb) nb.classList.remove('speaking');
        var t = document.getElementById('narrateBtnTxt');
        if (t) t.textContent = 'Narration';
    };

    var spoke = false;

    function doSpeak() {
        if (spoke) return;
        spoke = true;
        pickVoiceForLang(CURRENT_LANG, utt, langFallback);
        try {
            window.speechSynthesis.speak(utt);
        } catch (err) { /* anciens navigateurs */ }
        NARRATING = true;
        var nb = document.getElementById('narrateBtn');
        if (nb) nb.classList.add('speaking');
        var t = document.getElementById('narrateBtnTxt');
        if (t) t.textContent = 'Arrêter ■';
    }

    if (window.speechSynthesis.getVoices().length) {
        doSpeak();
    } else {
        window.speechSynthesis.addEventListener(
            'voiceschanged',
            function onVoices() {
                window.speechSynthesis.removeEventListener('voiceschanged', onVoices);
                doSpeak();
            }
        );
        window.setTimeout(doSpeak, 450);
        try {
            window.speechSynthesis.getVoices();
        } catch (e2) { /* noop */ }
    }
}

function stopNarration() {
    if (window.speechSynthesis) window.speechSynthesis.cancel();
    NARRATING = false;
    var nb = document.getElementById('narrateBtn');
    if (nb) {
        nb.classList.remove('speaking');
        document.getElementById('narrateBtnTxt').textContent = 'Narration';
    }
}

function stopCam() {
    ACTIVE = false;
    if (SCAN_TIMER) { clearInterval(SCAN_TIMER); SCAN_TIMER = null; }
    if (CAM) { CAM.getTracks().forEach(function(t){ t.stop(); }); CAM = null; }
    var v = document.getElementById('qrVideo');
    if (v) { v.srcObject = null; v.style.display = 'none'; }
}

function resetUi() {
    ACTIVE = false;
    LAST   = null;

    document.getElementById('qrIdle').style.display  = 'flex';
    document.getElementById('qrFrame').style.display = 'none';
    document.getElementById('qrRes').style.display   = 'none';

    var btn = document.getElementById('qrBtn');
    btn.style.display = 'block';
    btn.disabled      = false;
    var qa = SITE_I18N[SITE_LANG] || SITE_I18N.fr;
    btn.textContent   = qa.qr_activate;
    btn.onclick       = startCam;

    setStatus('idle', qrStatusIdleMsg());
}

/* Helper status UI */
function setStatus(type, msg) {
    var dot  = document.getElementById('qrDot');
    var text = document.getElementById('qrMsg');
    dot.className  = 'qr-dot';
    text.textContent = msg;

    var col = {
        scanning : 'rgba(255,255,255,0.55)',
        ok       : '#c8a84b',
        err      : '#f87171',
        wait     : 'rgba(255,255,255,0.3)',
        idle     : 'rgba(255,255,255,0.25)'
    };

    text.style.color = col[type] || col.idle;

    if (type === 'scanning') dot.classList.add('scanning');
    if (type === 'ok')       dot.classList.add('ok');
    if (type === 'err')      dot.classList.add('err');
}

/* ── Plan du musée : parcours par étage ── */
var MUSEUM_FLOOR_GEOM = {
    rdc: {
        path: 'M18,70 L22,36 C28,18 48,18 56,30 C64,42 52,52 66,58 C76,64 82,44 88,38',
        points: [
            { top: '24%', left: '26%' },
            { top: '36%', left: '48%' },
            { top: '50%', left: '34%' },
            { top: '58%', left: '64%' },
            { top: '44%', left: '78%' },
        ]
    },
    e1: {
        path: 'M14,58 Q32,24 54,34 Q68,40 78,52 L86,36',
        points: [
            { top: '26%', left: '22%' },
            { top: '34%', left: '44%' },
            { top: '46%', left: '58%' },
            { top: '56%', left: '36%' },
            { top: '40%', left: '76%' },
        ]
    },
    e2: {
        path: 'M20,68 C28,40 40,28 56,36 S78,48 84,32',
        points: [
            { top: '28%', left: '30%' },
            { top: '40%', left: '52%' },
            { top: '52%', left: '40%' },
            { top: '38%', left: '72%' },
            { top: '62%', left: '58%' },
        ]
    },
    e3: {
        path: 'M16,62 L28,32 Q48,20 62,38 Q74,52 88,44',
        points: [
            { top: '30%', left: '24%' },
            { top: '42%', left: '46%' },
            { top: '54%', left: '34%' },
            { top: '48%', left: '70%' },
            { top: '64%', left: '56%' },
        ]
    },
};

var MUSEUM_FLOOR_LABELS = {
    fr: {
        rdc: {
            title: 'Parcours — Rez-de-chaussée',
            note: 'Hall d\'accueil, orientation et accès aux expositions du niveau principal.',
            points: [
                'Accueil & billetterie',
                'Panneau d\'orientation',
                'Boutique & catalogues',
                'Salle d\'exposition temporaire',
                'Escaliers & ascenseur',
            ]
        },
        e1: {
            title: 'Parcours — Chronologie (étage 1)',
            note: 'Frise chronologique et salles thématiques de l\'histoire militaire nationale.',
            points: [
                'Pré-colonial & réformes',
                'Période coloniale',
                'Guerre de libération',
                'Indépendance & ANP',
                'Salle documents & cartes',
            ]
        },
        e2: {
            title: 'Parcours — Collections (étage 2)',
            note: 'Uniformes, armements légers, insignes et matériel de terrain présentés par époque.',
            points: [
                'Uniformes & décorations',
                'Armes légères',
                'Équipement de campagne',
                'Maquettes véhicules',
                'Espace tactile enfants',
            ]
        },
        e3: {
            title: 'Parcours — Archives & mémoire (étage 3)',
            note: 'Archives photographiques, témoignages et espace de recherche pour le public averti.',
            points: [
                'Photothèque',
                'Témoignages audio',
                'Salle de consultation',
                'Drapeaux & emblèmes',
                'Réservation groupes',
            ]
        },
    },
    ar: {
        rdc: {
            title: 'المسار — الطابق الأرضي',
            note: 'قاعة الاستقبال والتوجيه والوصول إلى معارض المستوى الرئيسي.',
            points: [
                'الاستقبال والتذاكر',
                'لوحة المعلومات',
                'المتجر',
                'العرض المؤقت',
                'السلالم والمصعد',
            ]
        },
        e1: {
            title: 'المسار — الخط الزمني (طابق 1)',
            note: 'الخط الزمني وقاعات مواضيع التاريخ العسكري الوطني.',
            points: [
                'ما قبل الاستعمار',
                'المرحلة الاستعمارية',
                'جبهة التحرير',
                'الاستقلال',
                'قاعة الوثائق',
            ]
        },
        e2: {
            title: 'المسار — المجموعات (طابق 2)',
            note: 'الأزياء والأسلحة الخفيفة والشارات والمعدات الميدانية حسب الحقبة.',
            points: [
                'الأزياء العسكرية',
                'الأسلحة الخفيفة',
                'المعدات الميدانية',
                'الوسائل المدرعة',
                'زاوية تفاعلية',
            ]
        },
        e3: {
            title: 'المسار — الأرشيف والذاكرة (طابق 3)',
            note: 'أرشيف الصور، شهود حيّون ومساحة بحث للجمهور المتخصص.',
            points: [
                'أرشيف الصور',
                'شهادات حية',
                'قاعة البحث',
                'الرموز والأعلام',
                'خصومات للمجموعات',
            ]
        },
    },
    en: {
        rdc: {
            title: 'Route — Ground floor',
            note: 'Welcome hall, orientation and access to the main exhibitions.',
            points: [
                'Reception & tickets',
                'Orientation panel',
                'Shop & catalogs',
                'Temporary exhibition hall',
                'Stairs & elevator',
            ]
        },
        e1: {
            title: 'Route — Timeline (floor 1)',
            note: 'Chronological displays and thematic rooms on national military history.',
            points: [
                'Pre-colonial era',
                'Colonial period',
                'War of liberation',
                'Independence & NPA',
                'Documents & maps room',
            ]
        },
        e2: {
            title: 'Route — Collections (floor 2)',
            note: 'Uniforms, light weapons, insignia and field equipment by period.',
            points: [
                'Uniforms & decorations',
                'Light weapons',
                'Field equipment',
                'Vehicle models',
                'Children\'s tactile corner',
            ]
        },
        e3: {
            title: 'Route — Archives & memory (floor 3)',
            note: 'Photo archives, audio testimonies and a research area for keen visitors.',
            points: [
                'Photo library',
                'Audio testimonies',
                'Study room',
                'Flags & emblems',
                'Group bookings',
            ]
        },
    },
    es: {
        rdc: {
            title: 'Recorrido — Planta baja',
            note: 'Hall de bienvenida, orientación y acceso a las exposiciones principales.',
            points: [
                'Recepción y taquillas',
                'Panel de orientación',
                'Tienda y catálogos',
                'Sala de exposición temporal',
                'Escaleras y ascensor',
            ]
        },
        e1: {
            title: 'Recorrido — Cronología (planta 1)',
            note: 'Friso cronológico y salas temáticas sobre la historia militar nacional.',
            points: [
                'Pre colonial y reformas',
                'Periodo colonial',
                'Guerra de liberación',
                'Independencia y ENP',
                'Sala de documentos y mapas',
            ]
        },
        e2: {
            title: 'Recorrido — Colecciones (planta 2)',
            note: 'Uniformes, armas ligeras, insignias y equipo de campaña por época.',
            points: [
                'Uniformes y condecoraciones',
                'Armas ligeras',
                'Equipo de campaña',
                'Maquetas de vehículos',
                'Zona táctil infantil',
            ]
        },
        e3: {
            title: 'Recorrido — Archivos y memoria (planta 3)',
            note: 'Archivo fotográfico, testimonios en audio y sala de investigación.',
            points: [
                'Fototeca',
                'Testimonios de audio',
                'Sala de consulta',
                'Banderas y emblemas',
                'Reservas para grupos',
            ]
        },
    },
    zh: {
        rdc: {
            title: '参观路线 — 一层',
            note: '迎宾大厅、导览信息与主展区通道。',
            points: [
                '票务与问询',
                '导览指示牌',
                '文创商店与目录',
                '临时展厅',
                '楼梯与电梯',
            ]
        },
        e1: {
            title: '参观路线 — 二层（年代线）',
            note: '国家军事史主题展陈与时间轴。',
            points: [
                '殖民前与变革',
                '殖民时期',
                '解放战争',
                '独立与人军',
                '文献与地图厅',
            ]
        },
        e2: {
            title: '参观路线 — 三层（藏品）',
            note: '各时期军服、轻武器、徽章与野战装备。',
            points: [
                '军服与勋章',
                '轻武器',
                '野战装备',
                '车辆模型',
                '儿童互动角',
            ]
        },
        e3: {
            title: '参观路线 — 四层（档案与记忆）',
            note: '影像档案、口述史料与研习空间。',
            points: [
                '影像资料库',
                '口述历史',
                '查阅室',
                '旗帜与徽标',
                '团体预约',
            ]
        },
    },
    ru: {
        rdc: {
            title: 'Маршрут — 1-й этаж',
            note: 'Вестибюль, навигация и доступ к основным экспозициям.',
            points: [
                'Прием и билеты',
                'Информационный стенд',
                'Сувениры и каталоги',
                'Зал временных выставок',
                'Лестницы и лифт',
            ]
        },
        e1: {
            title: 'Маршрут — Хронология (2-й этаж)',
            note: 'Хронология и тематические залы национальной военной истории.',
            points: [
                'Доколониальный период',
                'Колониальный период',
                'Война за освобождение',
                'Независимость и ННА',
                'Зал документов и карт',
            ]
        },
        e2: {
            title: 'Маршрут — Коллекции (3-й этаж)',
            note: 'Формы, стрелковое оружие, знаки различия и полевое снаряжение по эпохам.',
            points: [
                'Форма и награды',
                'Стрелковое оружие',
                'Полевое снаряжение',
                'Макеты техники',
                'Детский интерактив',
            ]
        },
        e3: {
            title: 'Маршрут — Архивы и память (4-й этаж)',
            note: 'Фотоархив, аудиосвидетельства и зона для исследователей.',
            points: [
                'Фототека',
                'Аудиосвидетельства',
                'Читальный зал',
                'Флаги и эмблемы',
                'Групповые экскурсии',
            ]
        },
    },
};

function selectMuseumFloor(key) {
    CURRENT_MAP_FLOOR = key;
    var geom = MUSEUM_FLOOR_GEOM[key];
    if (!geom) return;

    var lang = SITE_LANG;
    var lblPack = MUSEUM_FLOOR_LABELS[lang] || MUSEUM_FLOOR_LABELS.fr;
    var lbl = lblPack[key];

    var titleEl = document.getElementById('mapBoardTitle');
    var pathEl = document.getElementById('mapRoutePath');
    var layer = document.getElementById('mapPointsLayer');
    var noteEl = document.getElementById('mapFloorNote');

    if (titleEl && lbl) titleEl.textContent = lbl.title;
    if (pathEl) pathEl.setAttribute('d', geom.path);
    if (noteEl && lbl) noteEl.textContent = lbl.note;

    if (layer && lbl && geom.points) {
        layer.innerHTML = '';
        geom.points.forEach(function(pos, idx) {
            var text = lbl.points[idx] || '';
            var wrap = document.createElement('div');
            wrap.className = 'map-point';
            wrap.style.top = pos.top;
            wrap.style.left = pos.left;
            var label = document.createElement('div');
            label.className = 'map-point-label';
            var name = document.createElement('span');
            name.className = 'map-point-name';
            name.textContent = text;
            label.appendChild(name);
            wrap.innerHTML = '<span class="map-point-dot"></span>';
            wrap.appendChild(label);
            layer.appendChild(wrap);
        });
    }

    document.querySelectorAll('.map-floor-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.getAttribute('data-floor') === key);
    });
}

(function() {
    var m = document.getElementById('qrManualId');
    if (m) {
        m.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); tryManualQr(); }
        });
    }
})();

</script>

</body>
</html>
