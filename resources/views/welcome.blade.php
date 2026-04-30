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
            --gold:       #c8a84b;
            --gold-lt:    #dfc278;
            --gold-dim:   rgba(200,168,75,0.15);
            --ivory:      #f3ecd9;
            --ink:        #1c1406;
            --ink-mid:    #3a2e18;
            --muted-gold: rgba(200,168,75,0.6);
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 36px;
        }

        .hdr-block {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .hdr-block.r { flex-direction: row-reverse; }

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
            height: calc(var(--vh, 1vh) * 100);
            min-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            padding-top: var(--header-h);
            background: #051c13;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 50% 0%, rgba(200,168,75,0.14), transparent 44%),
                repeating-linear-gradient(
                    0deg,
                    rgba(255,255,255,0.012) 0px,
                    rgba(255,255,255,0.012) 1px,
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
            animation: up 1.1s ease both;
        }

        @keyframes up {
            from { opacity:0; transform: translateY(22px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .hero-republic-ar {
            font-family: var(--fa);
            font-size: clamp(14px,1.9vw,20px);
            color: rgba(255,255,255,0.72);
            direction: rtl;
            margin-bottom: 5px;
        }

        .hero-republic-fr {
            font-family: var(--fh);
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--muted-gold);
            margin-bottom: 18px;
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

        .hero-ar {
            font-family: var(--fa);
            font-size: clamp(48px,7vw,76px);
            font-weight: 700;
            color: #fff;
            direction: rtl;
            line-height: 1.1;
            margin-bottom: 4px;
            text-shadow: 0 2px 30px rgba(0,0,0,0.45);
        }

        .hero-fr {
            font-family: var(--ff);
            font-size: clamp(26px,4.1vw,54px);
            font-weight: 400;
            font-style: italic;
            color: var(--gold-lt);
            margin-bottom: 18px;
            text-shadow: 0 0 24px rgba(200,168,75,0.25);
        }

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
            border: none;
            cursor: pointer;
            transition: all 0.22s;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            min-height: 52px;
            touch-action: manipulation;
        }

        .hbtn-gold {
            background: var(--gold);
            color: #1c1406;
            box-shadow: 0 8px 22px rgba(0,0,0,0.35);
        }
        .hbtn-gold:hover { background: var(--gold-lt); }

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
            background: transparent;
            color: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.28);
        }
        .hbtn-outline:hover {
            border-color: var(--gold);
            color: var(--gold-lt);
            background: var(--gold-dim);
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
        }

        @keyframes bounce {
            0%,100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(8px); }
        }

        /* ============================================================
           SECTION ORGANIGRAMME (fond ivoire)
        ============================================================ */
        .sec-org {
            background: var(--ivory);
            /* texture légère */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='6' height='6'%3E%3Crect width='6' height='6' fill='%23f3ecd9'/%3E%3Ccircle cx='1' cy='1' r='0.6' fill='%23ddd0b8' opacity='0.35'/%3E%3C/svg%3E");
            padding: 104px clamp(24px,6vw,80px);
            text-align: center;
        }

        .org-ar {
            font-family: var(--fa);
            font-size: clamp(22px,3.5vw,40px);
            font-weight: 700;
            color: var(--ink);
            direction: rtl;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .org-fr {
            font-family: var(--fu);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--ink-mid);
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
           SECTION PLAN (placeholder)
        ============================================================ */
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

        .ftr-ar {
            font-family: var(--fa);
            font-size: 18px;
            color: var(--gold-lt);
            direction: rtl;
            margin-bottom: 4px;
        }

        .ftr-fr {
            font-family: var(--ff);
            font-size: 15px;
            font-style: italic;
            color: var(--muted-gold);
            margin-bottom: 26px;
            text-shadow: 0 0 16px rgba(200,168,75,0.18);
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
            border-radius: 3px;
            color: rgba(255,255,255,0.45);
            font-size: 16px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.18s;
        }

        .qr-x:hover { background: rgba(255,255,255,0.1); color: #fff; }

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
            background: var(--gold);
            padding: 14px;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
            min-height: 50px;
            touch-action: manipulation;
        }

        #qrBtn:hover { background: var(--gold-lt); }
        #qrBtn:disabled { opacity: 0.35; cursor: not-allowed; }

        .qr-note {
            font-family: var(--fu);
            font-size: 11px;
            color: rgba(255,255,255,0.2);
            text-align: center;
            line-height: 1.7;
            margin-top: 10px;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 600px) {
            :root { --header-h: 60px; }
            .hdr { padding: 0 12px; }
            .hdr-block { gap: 8px; }
            .hdr-t1, .hdr-t2 { font-size: 8.5px; }
            .emblem { width: 44px; height: 44px; }
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
            .hero-ar { font-size: clamp(36px, 13vw, 56px); }
            .hero-fr { font-size: clamp(28px, 10vw, 40px); }
            .hero-desc { font-size: 15px; line-height: 1.55; margin-bottom: 22px; }
            .hbtn { max-width: 100%; font-size: 11px; min-height: 50px; }
            .hero-chevron { bottom: 10px; }
            .sec-org { padding: 64px 18px; }
            .os-line { width: 52px; }
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
                transform: scale(0.985);
                filter: brightness(0.98);
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
            border-radius: 2px;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .art-back:hover { background: var(--gold-dim); border-color: var(--gold); }

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
            border-radius: 2px;
            background: rgba(200,168,75,0.06);
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
            flex-wrap: wrap;
        }

        .lang-btn {
            font-family: var(--fu);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            padding: 6px 14px;
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 20px;
            background: rgba(200,168,75,0.05);
            color: rgba(255,255,255,0.45);
            cursor: pointer;
            transition: all 0.18s;
            white-space: nowrap;
            min-height: 32px;
            touch-action: manipulation;
        }
        .lang-btn:hover {
            border-color: rgba(200,168,75,0.55);
            color: var(--gold-lt);
            background: rgba(200,168,75,0.1);
        }
        .lang-btn.active {
            background: var(--gold);
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
            border-radius: 2px;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .art-narrate:hover { background: rgba(200,168,75,0.16); }
        .art-narrate.speaking {
            background: rgba(200,168,75,0.18);
            border-color: var(--gold);
            animation: pulse-btn 1.4s ease-in-out infinite;
        }
        @keyframes pulse-btn {
            0%,100% { box-shadow: 0 0 0 0 rgba(200,168,75,0.3); }
            50%      { box-shadow: 0 0 0 6px rgba(200,168,75,0); }
        }

        /* ── Body split layout ── */
        .art-body {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1px 420px;
            overflow: hidden;
        }

        /* Glowing gold divider */
        .art-divider-v {
            background: linear-gradient(
                to bottom,
                transparent 0%,
                rgba(200,168,75,0.5) 15%,
                var(--gold) 50%,
                rgba(200,168,75,0.5) 85%,
                transparent 100%
            );
            position: relative;
        }
        .art-divider-v::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            width: 9px; height: 9px;
            background: var(--gold);
            border-radius: 50%;
            box-shadow: 0 0 18px 6px rgba(200,168,75,0.45);
        }

        /* ── Left: visual panel ── */
        .art-visual {
            position: relative;
            background: radial-gradient(ellipse at 30% 40%, #0f2412 0%, #060e07 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            gap: 0;
        }

        /* Atmospheric pattern overlay */
        .art-visual::before {
            content: '';
            position: absolute; inset: 0;
            background-image: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 40px,
                rgba(200,168,75,0.015) 40px,
                rgba(200,168,75,0.015) 41px
            );
            pointer-events: none;
        }

        /* Figure portrait (historical person) */
        .art-figure-wrap {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            width: 100%;
            padding: 32px 40px 0;
        }

        .art-figure-img {
            width: clamp(180px, 22vw, 300px);
            aspect-ratio: 3/4;
            object-fit: cover;
            object-position: top;
            border: 2px solid rgba(200,168,75,0.4);
            box-shadow:
                0 0 0 6px rgba(200,168,75,0.06),
                0 0 60px rgba(200,168,75,0.18),
                0 30px 80px rgba(0,0,0,0.7);
            filter: sepia(0.18) contrast(1.05);
            display: block;
        }

        .art-figure-name {
            font-family: var(--fh);
            font-size: clamp(20px, 2.2vw, 30px);
            color: #fff;
            letter-spacing: 0.06em;
            text-align: center;
            margin-top: 22px;
            text-shadow: 0 2px 20px rgba(0,0,0,0.8);
        }
        .art-figure-dates {
            font-family: var(--ff);
            font-size: 13px;
            font-style: italic;
            color: var(--gold);
            letter-spacing: 0.1em;
            margin-top: 6px;
        }

        /* 3D viewer (for non-figure artifacts) */
        .art-viewer {
            flex: 1;
            width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .art-viewer model-viewer {
            width: 100%; height: 100%;
            background-color: transparent;
            --progress-bar-color: var(--gold);
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

        /* Linked artifacts strip (for historical figures) */
        .art-links {
            width: 100%;
            flex-shrink: 0;
            padding: 0 40px 28px;
            position: relative;
            z-index: 2;
        }
        .art-links-label {
            font-family: var(--fh);
            font-size: 9px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(200,168,75,0.45);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .art-links-label::before, .art-links-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(200,168,75,0.12);
        }
        .art-links-grid {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 6px;
            scrollbar-width: thin;
            scrollbar-color: rgba(200,168,75,0.2) transparent;
        }
        .art-link-card {
            flex-shrink: 0;
            width: 110px;
            cursor: pointer;
            border: 1px solid rgba(200,168,75,0.15);
            border-radius: 3px;
            overflow: hidden;
            background: rgba(200,168,75,0.04);
            transition: all 0.22s;
            position: relative;
        }
        .art-link-card:hover {
            border-color: var(--gold);
            box-shadow: 0 0 18px rgba(200,168,75,0.2);
            transform: translateY(-3px);
        }
        .art-link-card img {
            width: 100%;
            height: 70px;
            object-fit: cover;
            display: block;
            filter: brightness(0.75) sepia(0.1);
            transition: filter 0.22s;
        }
        .art-link-card:hover img { filter: brightness(0.9) sepia(0.05); }
        .art-link-caption {
            padding: 6px 8px;
            font-family: var(--ff);
            font-size: 10px;
            font-style: italic;
            color: rgba(200,168,75,0.6);
            line-height: 1.3;
            text-align: center;
        }

        /* ── Right: description panel ── */
        .art-desc-panel {
            background: #0a1a0c;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 40px 32px 40px;
            gap: 20px;
            scrollbar-width: thin;
            scrollbar-color: rgba(200,168,75,0.15) transparent;
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

        .art-meta {
            border: 1px solid rgba(200,168,75,0.1);
            border-radius: 3px;
            overflow: hidden;
            background: rgba(200,168,75,0.03);
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

        @media (max-width: 900px) {
            .art-body {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1px 1fr;
                overflow-y: auto;
            }
            .art-divider-v {
                background: linear-gradient(to right, transparent 0%, rgba(200,168,75,0.4) 50%, transparent 100%);
                height: 1px;
            }
            .art-divider-v::after { display: none; }
            .art-visual { min-height: 55vw; padding: 24px 20px 20px; }
            .art-desc-panel { padding: 28px 20px 36px; }
            .art-bar { padding: 0 14px; gap: 8px; }
            .art-back, .art-narrate { font-size: 10px; padding: 6px 10px; }
            .art-figure-img { width: clamp(130px, 30vw, 200px); }
            .art-links { padding: 0 20px 22px; }
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
            <div class="hdr-t1">Ministère de la</div>
            <div class="hdr-t2">Défense Nationale</div>
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

        <p class="hero-republic-ar">الجمهورية الجزائرية الديمقراطية الشعبية</p>
        <p class="hero-republic-fr">République Algérienne Démocratique et Populaire</p>

        <div class="div-gold" aria-hidden="true">
            <div class="dg-line"></div>
            <div class="dg-diamond"></div>
            <div class="dg-line r"></div>
        </div>

        <h1 class="hero-ar">المتحف المركزي للجيش</h1>
        <p class="hero-fr">Musée de l'armée centrale</p>

        <p class="hero-desc">
            Mémoire vivante de l'Armée Nationale Populaire —<br>
            un patrimoine d'honneur, de courage et d'histoire.
        </p>

        <div class="hero-btns">

            <button class="hbtn hbtn-gold" onclick="openQr()" type="button">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 3h7v7H3zM5 5v3h3V5zM14 3h7v7h-7zM16 5v3h3V5zM3 14h7v7H3zM5 16v3h3v-3zM14 14h2v2h-2zM18 14h2v2h-2zM14 18h2v2h-2zM18 18h2v2h-2z"/>
                </svg>
                Scanner le QR
            </button>

            <button class="hbtn hbtn-outline" onclick="goTo('sec-org')" type="button">
                Commencer la visite
            </button>

        </div>
    </div>

    <button class="hero-chevron" onclick="goTo('sec-org')" type="button" aria-label="Défiler">&#709;</button>

</section>


{{-- ────────────────────────────────────────────
     ORGANIGRAMME INSTITUTIONNEL (fond ivoire)
──────────────────────────────────────────── --}}
<section class="sec-org" id="sec-org">

    <div class="org-level">
        <p class="org-ar">وزارة الدفاع الوطني</p>
        <p class="org-fr">Ministère de la Défense Nationale</p>
    </div>

    <div class="org-sep" aria-hidden="true">
        <div class="os-line"></div>
        <div class="os-diamond"></div>
        <div class="os-line"></div>
    </div>

    <div class="org-level">
        <p class="org-ar">أركان الجيش الوطني الشعبي</p>
        <p class="org-fr">État-major de l'Armée Nationale Populaire</p>
    </div>

    <div class="org-sep" aria-hidden="true">
        <div class="os-line"></div>
        <div class="os-diamond"></div>
        <div class="os-line"></div>
    </div>

    <div class="org-level">
        <p class="org-ar">مديرية الإعلام والاتصال</p>
        <p class="org-fr">Direction de l'Information et de la Communication</p>
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

    <p class="ftr-ar">المتحف المركزي للجيش</p>
    <p class="ftr-fr">Musée de l'armée centrale</p>

    <p class="ftr-copy">© {{ date('Y') }} · ANP — Direction de l'Information et de la Communication</p>

</footer>


{{-- ────────────────────────────────────────────
     MODAL QR SCANNER
──────────────────────────────────────────── --}}
<div class="qr-ov" id="qrOverlay" onclick="bgClose(event)"
     role="dialog" aria-modal="true" aria-labelledby="qrTitle">

    <div class="qr-box">

        <div class="qr-top">
            <span class="qr-top-title" id="qrTitle">Scanner un code QR</span>
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
                    <p>Appuyez sur "Activer la caméra"<br>pour scanner un objet du musée</p>
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
                <div class="qr-res-lbl">Code détecté</div>
                <div class="qr-res-val" id="qrResVal">—</div>
            </div>

            <button id="qrBtn" onclick="startCam()" type="button">Activer la caméra</button>

            <p class="qr-note">Aucune donnée enregistrée · Accès local uniquement</p>

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

        {{-- 3D Viewer --}}
        <div class="art-viewer">
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

        {{-- Vertical gold divider (3rd column of grid) --}}
        <div class="art-divider-v" aria-hidden="true"></div>

        {{-- Description Panel --}}
        <div class="art-desc-panel">

            <div class="art-category" id="artCategory">— Objet de musée —</div>
            <div class="art-divider"></div>

            <h2 class="art-title ar-text" id="artTitle">…</h2>

            <p class="art-text ar-text" id="artDesc">…</p>

            <div class="art-meta" id="artMeta">
                <div class="art-meta-row">
                    <span class="art-meta-key">Réf.</span>
                    <span class="art-meta-val" id="artRef">—</span>
                </div>
                <div class="art-meta-row">
                    <span class="art-meta-key">Époque</span>
                    <span class="art-meta-val" id="artEpoque">—</span>
                </div>
                <div class="art-meta-row">
                    <span class="art-meta-key">Section</span>
                    <span class="art-meta-val" id="artSection">—</span>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ────────────────────────────────────────────
     JAVASCRIPT
──────────────────────────────────────────── --}}
<script>

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

/* Create a detached canvas once and reuse it */
var SCAN_CANVAS = document.createElement('canvas');
var SCAN_CTX    = SCAN_CANVAS.getContext('2d', { willReadFrequently: true });

function startCam() {
    var btn = document.getElementById('qrBtn');
    btn.disabled = true;
    setStatus('wait', 'Demande d\'accès en cours…');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        setStatus('err', 'Caméra non supportée');
        btn.disabled = false;
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: { ideal: 'environment' },
            width:  { ideal: 640 },
            height: { ideal: 480 }
        },
        audio: false
    })
    .then(function(stream) {
        CAM    = stream;
        ACTIVE = true;

        var v = document.getElementById('qrVideo');
        v.srcObject = stream;
        v.style.display = 'block';

        document.getElementById('qrIdle').style.display  = 'none';
        document.getElementById('qrFrame').style.display = 'block';
        document.getElementById('qrBtn').style.display   = 'none';

        setStatus('scanning', 'Caméra active — Cherche un QR code…');

        function beginScan() {
            if (SCAN_TIMER) return; /* guard: start only once */
            SCAN_TIMER = setInterval(scanFrame, 150); /* ~6 fps — fast enough, CPU-friendly */
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
        var m = {
            'NotAllowedError'     : 'Permission refusée — autorisez la caméra dans les paramètres',
            'NotFoundError'       : 'Aucune caméra détectée',
            'NotReadableError'    : 'Caméra occupée par une autre application',
            'OverconstrainedError': 'Contrainte vidéo non supportée'
        };
        setStatus('err', m[err.name] || 'Erreur caméra : ' + err.name);
    });
}

/* Called every 150 ms by setInterval */
function scanFrame() {
    if (!ACTIVE) { clearInterval(SCAN_TIMER); SCAN_TIMER = null; return; }

    var v = document.getElementById('qrVideo');
    if (!v || v.readyState < 2 || v.videoWidth === 0 || v.videoHeight === 0) return;

    /* Scale to ≤640px for speed without losing QR readability */
    var scale = Math.min(1, 640 / v.videoWidth);
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
            onDetected(code.data);
        }
    } catch(e) { /* ignore single-frame decode errors */ }
}

/* ═══════════════════════════════════════════════════════
   BASE DE DONNÉES DES ARTEFACTS
   Clé = contenu exact du QR code scané
   Ajoutez autant d'entrées que nécessaire.
═══════════════════════════════════════════════════════ */
var ARTIFACTS = {

  'artifact-001': {
    modelSrc : '',   /* ex: '/models/sabre.glb' — laisser vide = placeholder */
    category : 'Armes historiques · الأسلحة التاريخية',
    ref      : 'MAC-2024-001',
    epoque   : 'XIXe siècle',
    section  : 'Salle I — Guerre de libération',
    ar: {
      title: 'سيف الأمير عبد القادر',
      desc : 'سيف تاريخي يعود إلى القرن التاسع عشر، استخدمه الأمير عبد القادر الجزائري في معاركه ضد الاستعمار الفرنسي. يتميز بنقوش عربية دقيقة على النصل وقبضة من العاج المزيّن. يُعدّ هذا السيف رمزاً للمقاومة والكرامة الوطنية الجزائرية.'
    },
    es: {
      title: 'Espada del Emir Abd el-Qader',
      desc : 'Espada histórica del siglo XIX utilizada por el Emir Abd el-Qader al-Argelino en sus batallas contra la colonización francesa. Presenta delicadas inscripciones árabes en la hoja y una empuñadura de marfil decorado. Esta espada es un símbolo de resistencia y dignidad nacional argelina.'
    },
    fr: {
      title: 'Sabre de l\'Émir Abd el-Kader',
      desc : 'Sabre historique du XIXe siècle utilisé par l\'Émir Abd el-Kader al-Djazaïri dans ses combats contre la colonisation française. Il présente de fines inscriptions arabes sur la lame et une poignée en ivoire sculpté. Ce sabre est un symbole de résistance et de dignité nationale algérienne.'
    },
    en: {
      title: 'Sword of Emir Abd el-Kader',
      desc : 'A 19th-century historical sword used by Emir Abd el-Kader al-Jazairi in his battles against French colonisation. It features fine Arabic inscriptions on the blade and a carved ivory handle. This sword is a symbol of Algerian national resistance and dignity.'
    },
    zh: {
      title: '埃米尔·阿卜杜·卡迪尔之剑',
      desc : '这是一把19世纪的历史名剑，由阿尔及利亚埃米尔·阿卜杜勒·卡迪尔在抗击法国殖民统治的战役中使用。剑身刻有精细的阿拉伯铭文，手柄由雕刻象牙制成。这把剑是阿尔及利亚民族抵抗精神与尊严的象征。'
    }
  },

  'artifact-002': {
    modelSrc : '',
    category : 'Documents militaires · الوثائق العسكرية',
    ref      : 'MAC-2024-002',
    epoque   : '1954 — 1962',
    section  : 'Salle II — Guerre d\'indépendance',
    ar: {
      title: 'وثيقة بيان أول نوفمبر 1954',
      desc : 'نسخة أصلية من بيان أول نوفمبر 1954، الذي أعلن فيه جبهة التحرير الوطني اندلاع الثورة الجزائرية المسلحة ضد الاستعمار الفرنسي. يُعدّ هذا البيان وثيقة تأسيسية في تاريخ الجزائر الحديث، ويمثل انطلاقة كفاح الشعب الجزائري من أجل الحرية والاستقلال.'
    },
    es: {
      title: 'Documento del Manifiesto del 1 de Noviembre de 1954',
      desc : 'Copia original del Manifiesto del 1 de noviembre de 1954, en el que el Frente de Liberación Nacional proclamó el inicio de la revolución armada argelina contra el colonialismo francés. Este manifiesto es un documento fundacional en la historia moderna de Argelia.'
    },
    fr: {
      title: 'Proclamation du 1er Novembre 1954',
      desc : 'Exemplaire original de la proclamation du 1er novembre 1954, par laquelle le Front de Libération Nationale a déclaré le déclenchement de la Révolution algérienne armée contre le colonialisme français. Ce document fondateur marque l\'entrée de l\'Algérie dans la lutte pour son indépendance.'
    },
    en: {
      title: 'Proclamation of November 1st, 1954',
      desc : 'An original copy of the November 1st, 1954 proclamation, in which the National Liberation Front announced the start of the armed Algerian Revolution against French colonialism. This founding document marks Algeria\'s entry into the struggle for independence.'
    },
    zh: {
      title: '1954年11月1日宣言文件',
      desc : '这是1954年11月1日宣言的原件，民族解放阵线在此宣告了阿尔及利亚武装革命对抗法国殖民主义的开始。这份具有奠基意义的文件标志着阿尔及利亚人民争取独立斗争的开始。'
    }
  }

};

/* 若QR码未在数据库中，使用此通用占位 */
var ARTIFACT_FALLBACK = {
    modelSrc : '',
    category : 'Collection du musée · مجموعة المتحف',
    ref      : '—',
    epoque   : '—',
    section  : '—',
    ar: { title: 'قطعة أثرية', desc : 'هذه القطعة الأثرية جزء من المجموعة التاريخية للمتحف المركزي للجيش الوطني الشعبي. يرجى مراجعة أحد أعضاء الطاقم للحصول على مزيد من المعلومات.' },
    es: { title: 'Pieza de museo', desc : 'Esta pieza forma parte de la colección histórica del Museo Central del Ejército Nacional Popular. Consulte a un miembro del personal para obtener más información.' },
    fr: { title: 'Pièce de musée', desc : 'Cette pièce fait partie de la collection historique du Musée Central de l\'Armée Nationale Populaire. Veuillez vous adresser à un membre du personnel pour plus d\'informations.' },
    en: { title: 'Museum Artifact', desc : 'This item is part of the historical collection of the Central Museum of the National People\'s Army. Please consult a staff member for more information.' },
    zh: { title: '博物馆文物', desc : '该文物是人民军队中央博物馆历史馆藏的一部分。请向工作人员咨询更多信息。' }
};

/* 变量 */
var CURRENT_ARTIFACT = null;
var CURRENT_LANG     = 'ar';
var NARRATING        = false;

/* Code détecté — ouvre l'overlay artefact */
function onDetected(val) {
    ACTIVE = false;
    setStatus('ok', 'Code QR détecté !');
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
    if (!SCAN_TIMER) SCAN_TIMER = setInterval(scanFrame, 150);
}

/* ════════════════════════════════════════════════
   ARTIFACT OVERLAY
════════════════════════════════════════════════ */
function openArtifact(qrVal) {
    var data = ARTIFACTS[qrVal] || ARTIFACT_FALLBACK;
    CURRENT_ARTIFACT = data;
    CURRENT_LANG     = 'ar';
    NARRATING        = false;

    /* 3D model */
    var mv = document.getElementById('artModelViewer');
    var ph = document.getElementById('art3dPlaceholder');
    if (data.modelSrc) {
        mv.setAttribute('src', data.modelSrc);
        ph.classList.add('hidden');
    } else {
        mv.removeAttribute('src');
        ph.classList.remove('hidden');
    }

    /* Meta */
    document.getElementById('artCategory').textContent = data.category;
    document.getElementById('artRef').textContent      = data.ref;
    document.getElementById('artEpoque').textContent   = data.epoque;
    document.getElementById('artSection').textContent  = data.section;

    /* Language default = Arabic */
    _applyLang('ar');

    /* Narration button reset */
    var nb = document.getElementById('narrateBtn');
    nb.classList.remove('speaking');
    document.getElementById('narrateBtnTxt').textContent = 'Narration';

    /* Show overlay */
    var ov = document.getElementById('artOverlay');
    ov.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeArtifact() {
    stopNarration();
    document.getElementById('artOverlay').classList.remove('open');
    document.body.style.overflow = '';
    LAST = null; /* Allow rescanning same code */
}

function setLang(lang) {
    if (!CURRENT_ARTIFACT) return;
    CURRENT_LANG = lang;
    _applyLang(lang);
    /* If narrating, restart in new language */
    if (NARRATING) { stopNarration(); startNarration(); }
}

function _applyLang(lang) {
    var d        = CURRENT_ARTIFACT[lang] || CURRENT_ARTIFACT['en'];
    var isAr     = (lang === 'ar');
    var titleEl  = document.getElementById('artTitle');
    var descEl   = document.getElementById('artDesc');

    titleEl.textContent = d.title;
    descEl.textContent  = d.desc;

    /* Direction & font class */
    titleEl.className = 'art-title ' + (isAr ? 'ar-text' : 'ltr-text');
    descEl.className  = 'art-text '  + (isAr ? 'ar-text' : 'ltr-text');

    /* Highlight active lang button */
    document.querySelectorAll('.lang-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.lang === lang);
    });
}

/* ── Narration (Web Speech API) ── */
var LANG_BCP47 = {
    ar : 'ar-SA',
    es : 'es-ES',
    fr : 'fr-FR',
    en : 'en-US',
    zh : 'zh-CN'
};

function toggleNarrate() {
    if (NARRATING) { stopNarration(); } else { startNarration(); }
}

function startNarration() {
    if (!window.speechSynthesis || !CURRENT_ARTIFACT) return;
    var d    = CURRENT_ARTIFACT[CURRENT_LANG] || CURRENT_ARTIFACT['en'];
    var text = d.title + '. ' + d.desc;
    var utt  = new SpeechSynthesisUtterance(text);
    utt.lang = LANG_BCP47[CURRENT_LANG] || 'fr-FR';
    utt.rate = 0.92;

    utt.onend = utt.onerror = function() {
        NARRATING = false;
        var nb = document.getElementById('narrateBtn');
        nb.classList.remove('speaking');
        document.getElementById('narrateBtnTxt').textContent = 'Narration';
    };

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utt);
    NARRATING = true;

    var nb = document.getElementById('narrateBtn');
    nb.classList.add('speaking');
    document.getElementById('narrateBtnTxt').textContent = 'Arrêter ■';
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
    btn.textContent   = 'Activer la caméra';
    btn.onclick       = startCam;

    setStatus('idle', 'En attente');
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

</script>

</body>
</html>
