{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Administration — Connexion</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
/* ── Reset ─────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{height:100%}

/* ── Variables ─────────────────────────────── */
:root{
  --g:#0a1a0c;
  --g2:#0e2012;
  --g3:#132016;
  --gold:#c8a84b;
  --gold-lt:#dfc278;
  --gold-dk:#a8842e;
  --gold-dim:rgba(200,168,75,.15);
  --ivory:#f3ecd9;
  --ink:#1c1406;
  --err:#f87171;
  --fc:'Cinzel',serif;
  --ff:'Cormorant Garamond',Georgia,serif;
  --fu:'Source Sans 3',system-ui,sans-serif;
  --fa:'Amiri',serif;
}

/* ── Body / BG ─────────────────────────────── */
body{
  min-height:100vh;
  display:flex;
  font-family:var(--fu);
  color:var(--ivory);
  background:var(--g);
  overflow:hidden;
}

/* Animated gradient background */
.login-bg{
  position:fixed;inset:0;
  background:
    radial-gradient(ellipse 80% 60% at 20% 10%, rgba(200,168,75,.09) 0%, transparent 55%),
    radial-gradient(ellipse 60% 80% at 85% 80%, rgba(15,91,47,.25) 0%, transparent 55%),
    linear-gradient(160deg,#071208 0%,#0a1a0c 40%,#081a0e 100%);
  z-index:0;
}

/* Subtle grid lines */
.login-bg::before{
  content:'';
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(200,168,75,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(200,168,75,.03) 1px, transparent 1px);
  background-size:60px 60px;
}

/* Floating orbs */
.orb{
  position:absolute;
  border-radius:50%;
  filter:blur(80px);
  animation:drift 18s ease-in-out infinite alternate;
  pointer-events:none;
}
.orb-1{width:340px;height:340px;top:-80px;left:-100px;background:rgba(200,168,75,.07);animation-delay:0s}
.orb-2{width:260px;height:260px;bottom:-60px;right:-80px;background:rgba(15,91,47,.15);animation-delay:-7s}
.orb-3{width:200px;height:200px;top:50%;right:15%;background:rgba(200,168,75,.05);animation-delay:-13s}

@keyframes drift{
  from{transform:translate(0,0) scale(1)}
  to{transform:translate(30px,20px) scale(1.08)}
}

/* ── Layout wrapper ────────────────────────── */
.login-wrap{
  position:relative;z-index:1;
  display:flex;
  width:100%;
  min-height:100vh;
}

/* ── Left decorative panel (desktop only) ──── */
.login-panel{
  display:none;
  flex-direction:column;
  justify-content:space-between;
  padding:clamp(32px,4vw,56px);
  flex:0 0 44%;
  border-right:1px solid rgba(200,168,75,.12);
  background:
    linear-gradient(180deg,rgba(200,168,75,.04) 0%,transparent 60%),
    rgba(12,24,14,.45);
  backdrop-filter:blur(4px);
}

@media(min-width:900px){.login-panel{display:flex}}

.panel-logo-row{
  display:flex;align-items:center;gap:16px;
}
.panel-emblem{
  width:52px;height:52px;
  border-radius:50%;
  border:1px solid rgba(200,168,75,.35);
  display:flex;align-items:center;justify-content:center;
  background:rgba(200,168,75,.06);
}
.panel-emblem img{width:100%;height:100%;object-fit:contain;padding:6px}
.panel-brand{
  font-family:var(--fc);
  font-size:11px;letter-spacing:.18em;text-transform:uppercase;
  color:var(--gold);line-height:1.5;
}
.panel-brand span{display:block;color:rgba(200,168,75,.5);font-size:9.5px}

/* Central decorative ornament */
.panel-center{text-align:center;padding:40px 20px}
.panel-arabic{
  font-family:var(--fa);
  font-size:clamp(28px,4vw,44px);
  font-weight:700;
  color:rgba(200,168,75,.35);
  direction:rtl;
  line-height:1.3;
  margin-bottom:18px;
}
.panel-divider{
  display:flex;align-items:center;justify-content:center;
  gap:12px;margin:0 auto 18px;width:fit-content;
}
.pd-line{width:50px;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.25))}
.pd-line.r{background:linear-gradient(90deg,rgba(200,168,75,.25),transparent)}
.pd-diamond{width:6px;height:6px;background:rgba(200,168,75,.3);transform:rotate(45deg);flex-shrink:0}
.panel-quote{
  font-family:var(--ff);
  font-size:clamp(14px,1.5vw,18px);
  font-style:italic;
  color:rgba(243,236,217,.3);
  line-height:1.8;
  max-width:320px;
  margin:0 auto;
}

.panel-bottom{font-family:var(--fu);font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:rgba(200,168,75,.25)}

/* ── Right — Form side ─────────────────────── */
.login-form-side{
  flex:1;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:clamp(24px,5vw,60px);
}

.login-card{
  width:100%;
  max-width:400px;
  animation:cardIn .6s cubic-bezier(.22,1,.36,1) both;
}

@keyframes cardIn{
  from{opacity:0;transform:translateY(24px)}
  to{opacity:1;transform:translateY(0)}
}

/* Card top — mobile logo */
.card-logo-mobile{
  display:flex;
  align-items:center;
  gap:14px;
  margin-bottom:32px;
}
@media(min-width:900px){.card-logo-mobile{display:none}}
.card-logo-mobile .panel-emblem{width:42px;height:42px}

/* Heading block */
.card-eyebrow{
  font-family:var(--fc);
  font-size:10px;
  letter-spacing:.25em;
  text-transform:uppercase;
  color:rgba(200,168,75,.5);
  margin-bottom:10px;
}
.card-title{
  font-family:var(--ff);
  font-size:clamp(28px,4vw,38px);
  font-weight:300;
  font-style:italic;
  color:var(--ivory);
  line-height:1.15;
  margin-bottom:6px;
}
.card-title em{font-style:normal;color:var(--gold-lt)}
.card-subtitle{
  font-size:13px;
  color:rgba(243,236,217,.4);
  margin-bottom:36px;
  font-weight:300;
}

/* Error alert */
.alert-err{
  display:flex;
  align-items:flex-start;
  gap:10px;
  padding:12px 14px;
  border-radius:6px;
  background:rgba(248,113,113,.08);
  border:1px solid rgba(248,113,113,.25);
  color:#fca5a5;
  font-size:13.5px;
  margin-bottom:22px;
  animation:shake .35s ease both;
}
.alert-err svg{width:16px;height:16px;flex-shrink:0;stroke:#fca5a5;fill:none;stroke-width:1.5;margin-top:1px}
@keyframes shake{
  0%,100%{transform:translateX(0)}
  20%{transform:translateX(-5px)}
  40%{transform:translateX(4px)}
  60%{transform:translateX(-3px)}
  80%{transform:translateX(2px)}
}

/* Form fields */
.field{margin-bottom:20px}
.field-label{
  display:block;
  font-family:var(--fc);
  font-size:9.5px;
  letter-spacing:.2em;
  text-transform:uppercase;
  color:rgba(200,168,75,.5);
  margin-bottom:9px;
}
.field-wrap{position:relative}
.field-icon{
  position:absolute;
  left:14px;top:50%;transform:translateY(-50%);
  width:16px;height:16px;
  stroke:rgba(200,168,75,.3);fill:none;stroke-width:1.5;
  pointer-events:none;
  transition:stroke .2s;
}
.field-wrap:focus-within .field-icon{stroke:rgba(200,168,75,.7)}
.field-input{
  width:100%;
  padding:13px 14px 13px 42px;
  border:1px solid rgba(200,168,75,.18);
  border-radius:6px;
  background:rgba(0,0,0,.2);
  color:var(--ivory);
  font-family:var(--fu);
  font-size:15px;
  outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  -webkit-appearance:none;
}
.field-input::placeholder{color:rgba(243,236,217,.18)}
.field-input:focus{
  border-color:rgba(200,168,75,.5);
  background:rgba(0,0,0,.3);
  box-shadow:0 0 0 3px rgba(200,168,75,.07);
}

/* Password toggle */
.field-eye{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;padding:4px;
  color:rgba(243,236,217,.25);
  transition:color .2s;
  display:flex;align-items:center;
}
.field-eye:hover{color:rgba(200,168,75,.6)}
.field-eye svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.5}

/* Remember + forgot */
.field-footer{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:28px;
  flex-wrap:wrap;gap:10px;
}
.field-check{
  display:flex;align-items:center;gap:8px;
  font-size:13px;color:rgba(243,236,217,.45);
  cursor:pointer;user-select:none;
}
.field-check input[type=checkbox]{
  width:15px;height:15px;
  appearance:none;-webkit-appearance:none;
  border:1px solid rgba(200,168,75,.28);
  border-radius:3px;
  background:transparent;
  cursor:pointer;
  position:relative;
  flex-shrink:0;
  transition:border-color .2s,background .2s;
}
.field-check input[type=checkbox]:checked{
  background:var(--gold);border-color:var(--gold);
}
.field-check input[type=checkbox]:checked::after{
  content:'';
  position:absolute;
  left:4px;top:1px;
  width:5px;height:9px;
  border:1.5px solid #1c1406;
  border-left:none;border-top:none;
  transform:rotate(45deg);
}

/* Submit button */
.btn-submit{
  width:100%;
  display:flex;align-items:center;justify-content:center;gap:10px;
  padding:15px 24px;
  font-family:var(--fc);
  font-size:11px;font-weight:600;
  letter-spacing:.22em;text-transform:uppercase;
  color:var(--ink);
  background:linear-gradient(135deg,var(--gold-lt) 0%,var(--gold) 50%,var(--gold-dk) 100%);
  border:none;border-radius:6px;
  cursor:pointer;
  box-shadow:0 6px 20px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.12);
  transition:filter .2s,transform .15s,box-shadow .2s;
  position:relative;overflow:hidden;
}
.btn-submit::before{
  content:'';
  position:absolute;top:0;left:-110%;width:60%;height:100%;
  background:linear-gradient(100deg,transparent,rgba(255,255,255,.25),transparent);
  transition:left .5s ease;
}
.btn-submit:hover::before{left:140%}
.btn-submit:hover{filter:brightness(1.06);transform:translateY(-1px);box-shadow:0 10px 28px rgba(0,0,0,.35)}
.btn-submit:active{transform:translateY(0)}
.btn-submit.loading{pointer-events:none;opacity:.7}
.btn-submit svg{width:15px;height:15px;fill:currentColor;flex-shrink:0}

/* Spinner */
.spinner{
  width:16px;height:16px;
  border:2px solid rgba(28,20,6,.3);
  border-top-color:var(--ink);
  border-radius:50%;
  animation:spin .7s linear infinite;
  display:none;
  flex-shrink:0;
}
@keyframes spin{to{transform:rotate(360deg)}}

/* Back link */
.back-link{
  display:flex;align-items:center;justify-content:center;gap:6px;
  margin-top:24px;
  font-size:13px;color:rgba(200,168,75,.35);
  text-decoration:none;
  transition:color .2s;
}
.back-link:hover{color:rgba(200,168,75,.7)}
.back-link svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2}
</style>
</head>
<body>

<div class="login-bg">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
</div>

<div class="login-wrap">

  {{-- ── Left decorative panel ── --}}
  <div class="login-panel">
    <div class="panel-logo-row">
      <div class="panel-emblem">
        <img src="{{ asset('images/anp.png') }}" alt="ANP"
             onerror="this.style.display='none'">
      </div>
      <div class="panel-brand">
        Musée Militaire National
        <span>Administration système</span>
      </div>
    </div>

    <div class="panel-center">
      <p class="panel-arabic">المتحف المركزي للجيش</p>
      <div class="panel-divider">
        <div class="pd-line"></div>
        <div class="pd-diamond"></div>
        <div class="pd-line r"></div>
      </div>
      <p class="panel-quote">
        Gardien de la mémoire nationale —<br>
        un patrimoine d'honneur et de courage.
      </p>
    </div>

    <div class="panel-bottom">© {{ date('Y') }} · ANP — DIC</div>
  </div>

  {{-- ── Form side ── --}}
  <div class="login-form-side">
    <div class="login-card">

      {{-- Mobile logo --}}
      <div class="card-logo-mobile">
        <div class="panel-emblem" style="width:42px;height:42px">
          <img src="{{ asset('images/anp.png') }}" alt="ANP" onerror="this.style.display='none'">
        </div>
        <div class="panel-brand" style="font-size:10px">
          Musée Militaire
          <span>Administration</span>
        </div>
      </div>

      <p class="card-eyebrow">Espace sécurisé</p>
      <h1 class="card-title">Bon<em>jour</em></h1>
      <p class="card-subtitle">Connectez-vous pour accéder au panneau d'administration.</p>

      {{-- Error --}}
      @if($errors->any())
      <div class="alert-err" role="alert">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
        @csrf

        {{-- Email --}}
        <div class="field">
          <label class="field-label" for="email">Adresse électronique</label>
          <div class="field-wrap">
            <svg class="field-icon" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8l10 6 10-6"/></svg>
            <input class="field-input" id="email" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username" autofocus
                   placeholder="admin@musee.dz"/>
          </div>
        </div>

        {{-- Password --}}
        <div class="field">
          <label class="field-label" for="password">Mot de passe</label>
          <div class="field-wrap">
            <svg class="field-icon" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            <input class="field-input" id="password" type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••"/>
            <button type="button" class="field-eye" id="eyeBtn" aria-label="Afficher le mot de passe">
              <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        {{-- Remember --}}
        <div class="field-footer">
          <label class="field-check">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            Se souvenir de moi
          </label>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <div class="spinner" id="spinner"></div>
          <svg id="btnIcon" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
          Accéder au tableau de bord
        </button>
      </form>

      <a class="back-link" href="{{ url('/') }}">
        <svg viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Retour au site public
      </a>

    </div>
  </div>
</div>

<script>
/* Password toggle */
(function(){
  var btn=document.getElementById('eyeBtn');
  var inp=document.getElementById('password');
  var ico=document.getElementById('eyeIcon');
  if(!btn)return;
  var showPath='<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  var hidePath='<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  var shown=false;
  btn.addEventListener('click',function(){
    shown=!shown;
    inp.type=shown?'text':'password';
    ico.innerHTML=shown?hidePath:showPath;
  });
})();

/* Submit loading state */
(function(){
  var form=document.getElementById('loginForm');
  var btn=document.getElementById('submitBtn');
  var spin=document.getElementById('spinner');
  var icon=document.getElementById('btnIcon');
  if(!form)return;
  form.addEventListener('submit',function(){
    btn.classList.add('loading');
    spin.style.display='block';
    icon.style.display='none';
  });
})();
</script>
</body>
</html>
