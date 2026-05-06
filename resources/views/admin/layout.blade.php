{{-- resources/views/admin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>@yield('title','Admin') — Musée Militaire</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
/* ── Reset ──────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{height:100%}

/* ── Variables ──────────────────────────────── */
:root{
  --bg:#080f09;
  --panel:#0e1f10;
  --panel2:#0c1b0e;
  --border:rgba(200,168,75,.12);
  --gold:#c8a84b;
  --gold-lt:#dfc278;
  --gold-dk:#a8842e;
  --gold-dim:rgba(200,168,75,.12);
  --gold-hover:rgba(200,168,75,.09);
  --text:#e8e4d9;
  --muted:rgba(232,228,217,.45);
  --muted2:rgba(232,228,217,.25);
  --red:#b02a2a;
  --sidebar-w:248px;
  --topbar-h:60px;
  --fc:'Cinzel',serif;
  --ff:'Cormorant Garamond',Georgia,serif;
  --fu:'Source Sans 3',system-ui,sans-serif;
  --fa:'Amiri',serif;
}

/* ── Base ───────────────────────────────────── */
body{
  font-family:var(--fu);
  background:var(--bg);
  color:var(--text);
  min-height:100vh;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
}

/* ── TOPBAR (mobile) ────────────────────────── */
.topbar{
  display:none;
  position:fixed;top:0;left:0;right:0;
  height:var(--topbar-h);
  z-index:60;
  background:var(--panel);
  border-bottom:1px solid var(--border);
  padding:0 16px;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}
@media(max-width:900px){.topbar{display:flex}}

.topbar-brand{
  font-family:var(--fc);
  font-size:11px;
  letter-spacing:.14em;
  text-transform:uppercase;
  color:var(--gold);
}

.topbar-burger{
  width:38px;height:38px;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;
  background:var(--gold-dim);
  border:1px solid var(--border);
  border-radius:6px;
  cursor:pointer;
  flex-shrink:0;
}
.topbar-burger span{
  width:16px;height:1.5px;
  background:var(--gold);
  border-radius:2px;
  transition:transform .25s,opacity .25s;
  transform-origin:center;
}
body.sidebar-open .topbar-burger span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
body.sidebar-open .topbar-burger span:nth-child(2){opacity:0}
body.sidebar-open .topbar-burger span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}

/* ── SIDEBAR OVERLAY (mobile) ───────────────── */
.sidebar-overlay{
  display:none;
  position:fixed;inset:0;
  z-index:49;
  background:rgba(0,0,0,.65);
  backdrop-filter:blur(3px);
  opacity:0;
  transition:opacity .3s;
}
body.sidebar-open .sidebar-overlay{opacity:1}
@media(max-width:900px){.sidebar-overlay{display:block;pointer-events:none}}
body.sidebar-open .sidebar-overlay{pointer-events:auto}

/* ── SIDEBAR ────────────────────────────────── */
.sidebar{
  position:fixed;
  top:0;bottom:0;left:0;
  width:var(--sidebar-w);
  z-index:50;
  display:flex;flex-direction:column;
  background:linear-gradient(180deg,#0e2012 0%,#091508 100%);
  border-right:1px solid var(--border);
  padding:0;
  transition:transform .3s cubic-bezier(.22,1,.36,1);
  overflow:hidden;
}

@media(max-width:900px){
  .sidebar{
    transform:translateX(-100%);
    box-shadow:8px 0 40px rgba(0,0,0,.5);
  }
  body.sidebar-open .sidebar{transform:translateX(0)}
}

/* Sidebar shine line */
.sidebar::after{
  content:'';
  position:absolute;
  top:0;right:0;bottom:0;
  width:1px;
  background:linear-gradient(180deg,transparent,rgba(200,168,75,.15) 30%,rgba(200,168,75,.15) 70%,transparent);
  pointer-events:none;
}

/* ── Sidebar Header ─────────────────────────── */
.sb-head{
  padding:24px 20px 20px;
  border-bottom:1px solid var(--border);
  flex-shrink:0;
}
.sb-logo-row{display:flex;align-items:center;gap:12px;margin-bottom:14px}
.sb-emblem{
  width:44px;height:44px;
  border-radius:50%;
  border:1px solid rgba(200,168,75,.38);
  display:flex;align-items:center;justify-content:center;
  background:radial-gradient(circle at 35% 30%,rgba(200,168,75,.12),rgba(8,22,14,.92));
  flex-shrink:0;
  box-shadow:
    0 0 0 1px rgba(0,0,0,.35) inset,
    0 4px 14px rgba(0,0,0,.3),
    0 0 22px rgba(200,168,75,.06);
}
.sb-emblem img{
  width:100%;height:100%;object-fit:cover;object-position:center;
  padding:0;flex-shrink:0;
  transform:scale(1.08);
  filter:drop-shadow(0 1px 2px rgba(0,0,0,.35))
}
.sb-brand{
  font-family:var(--fc);
  font-size:10px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--gold);line-height:1.45;
}
.sb-brand span{display:block;color:rgba(200,168,75,.4);font-size:9px;margin-top:1px}

.sb-arabic{
  font-family:var(--fa);
  font-size:14px;font-weight:700;
  color:rgba(200,168,75,.25);
  direction:rtl;text-align:right;
}

/* ── Sidebar Nav ────────────────────────────── */
.sb-nav{
  flex:1;
  overflow-y:auto;
  padding:16px 12px;
  scrollbar-width:thin;
  scrollbar-color:rgba(200,168,75,.1) transparent;
}
.sb-nav::-webkit-scrollbar{width:3px}
.sb-nav::-webkit-scrollbar-track{background:transparent}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(200,168,75,.15);border-radius:3px}

.sb-section-label{
  font-family:var(--fc);
  font-size:8.5px;
  letter-spacing:.2em;
  text-transform:uppercase;
  color:rgba(200,168,75,.25);
  padding:12px 8px 6px;
  display:block;
}

.sb-link{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 10px;
  border-radius:6px;
  color:var(--muted);
  text-decoration:none;
  font-size:13.5px;font-weight:500;
  margin-bottom:2px;
  transition:background .15s,color .15s;
  position:relative;
}
.sb-link svg{
  width:16px;height:16px;
  stroke:currentColor;fill:none;stroke-width:1.5;
  flex-shrink:0;opacity:.7;
  transition:opacity .15s;
}
.sb-link:hover{background:var(--gold-hover);color:var(--text)}
.sb-link:hover svg{opacity:1}
.sb-link.active{
  background:rgba(200,168,75,.12);
  color:var(--gold-lt);
}
.sb-link.active svg{opacity:1;stroke:var(--gold)}
.sb-link.active::before{
  content:'';
  position:absolute;
  left:0;top:6px;bottom:6px;
  width:2px;border-radius:2px;
  background:var(--gold);
}

.sb-link-badge{
  margin-left:auto;
  font-size:10px;font-weight:700;
  letter-spacing:.06em;
  padding:2px 7px;
  border-radius:999px;
  background:rgba(200,168,75,.15);
  color:var(--gold);
  flex-shrink:0;
}

/* ── Nav hiérarchie (groupe Artefacts) ───────── */
.sb-nav-group{
  margin:4px 0 14px;
  padding-bottom:10px;
  border-bottom:1px solid rgba(200,168,75,.08);
}
.sb-group-title{
  padding:8px 8px 10px;
  font-family:var(--fc);
  font-size:8.5px;
  letter-spacing:.2em;
  text-transform:uppercase;
  color:rgba(200,168,75,.32);
}
.sb-sublink{
  display:flex;
  align-items:center;
  gap:10px;
  padding:9px 10px 9px 16px;
  margin:0 0 3px 6px;
  border-left:2px solid rgba(200,168,75,.12);
  border-radius:0 7px 7px 0;
  color:var(--muted);
  text-decoration:none;
  font-size:13px;
  font-weight:500;
  transition:background .15s,color .15s,border-color .15s;
  position:relative;
}
.sb-sublink svg{
  width:15px;height:15px;
  stroke:currentColor;fill:none;stroke-width:1.5;
  flex-shrink:0;opacity:.65;
}
.sb-sublink:hover{
  background:var(--gold-hover);
  color:var(--text);
  border-left-color:rgba(200,168,75,.35);
}
.sb-sublink:hover svg{opacity:.9}
.sb-sublink.active{
  background:rgba(200,168,75,.11);
  color:var(--gold-lt);
  border-left-color:var(--gold);
}
.sb-sublink.active svg{opacity:1;stroke:var(--gold)}

/* ── Sidebar Footer ─────────────────────────── */
.sb-foot{
  padding:16px 12px 20px;
  border-top:1px solid var(--border);
  flex-shrink:0;
}
.sb-user{
  display:flex;align-items:center;gap:10px;
  padding:10px 10px;border-radius:6px;
  background:rgba(0,0,0,.18);
  margin-bottom:10px;
}
.sb-user-avatar{
  width:34px;height:34px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--gold-dk),var(--gold));
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
  font-family:var(--fc);
  font-size:12px;
  color:var(--bg);
  font-weight:600;
}
.sb-user-info{flex:1;min-width:0}
.sb-user-name{
  font-size:13px;font-weight:500;
  color:var(--text);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.sb-user-role{font-size:11px;color:var(--muted2);margin-top:1px}

.sb-logout{
  display:flex;align-items:center;gap:8px;
  width:100%;padding:9px 10px;
  border:1px solid rgba(200,168,75,.12);
  border-radius:6px;
  background:transparent;
  color:var(--muted);
  font-family:var(--fu);font-size:12.5px;font-weight:500;
  letter-spacing:.04em;
  cursor:pointer;
  transition:background .15s,color .15s,border-color .15s;
}
.sb-logout svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:1.5;opacity:.6;transition:opacity .15s}
.sb-logout:hover{background:rgba(180,30,30,.12);color:#fca5a5;border-color:rgba(180,30,30,.25)}
.sb-logout:hover svg{opacity:1}

/* ── MAIN CONTENT ───────────────────────────── */
.main-wrap{
  margin-left:var(--sidebar-w);
  min-height:100vh;
  display:flex;flex-direction:column;
  transition:margin .3s;
}
@media(max-width:900px){
  .main-wrap{margin-left:0;padding-top:var(--topbar-h)}
}

.main-inner{
  flex:1;
  padding:clamp(20px,3vw,36px);
}

/* ── Toast system ───────────────────────────── */
.toast-container{
  position:fixed;
  bottom:24px;right:24px;
  z-index:200;
  display:flex;flex-direction:column;gap:10px;
  pointer-events:none;
}
@media(max-width:600px){
  .toast-container{bottom:16px;right:16px;left:16px}
}
.toast{
  display:flex;align-items:center;gap:10px;
  padding:12px 16px;
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:8px;
  box-shadow:0 8px 24px rgba(0,0,0,.4);
  font-size:13.5px;
  color:var(--text);
  max-width:360px;
  pointer-events:auto;
  animation:toastIn .3s cubic-bezier(.22,1,.36,1) both;
  transition:opacity .25s,transform .25s;
}
.toast.out{opacity:0;transform:translateX(20px)}
@keyframes toastIn{
  from{opacity:0;transform:translateX(20px)}
  to{opacity:1;transform:translateX(0)}
}
.toast-dot{
  width:8px;height:8px;border-radius:50%;flex-shrink:0;
}
.toast.success .toast-dot{background:#4ade80}
.toast.error   .toast-dot{background:#f87171}
.toast.info    .toast-dot{background:var(--gold)}

/* ── Shared page components (used in child views) ── */
.page-head{
  display:flex;flex-wrap:wrap;align-items:flex-start;
  justify-content:space-between;gap:16px;
  margin-bottom:28px;
}
.page-head-left h1{
  font-family:var(--ff);
  font-size:clamp(24px,3.5vw,36px);
  font-weight:300;font-style:italic;
  color:var(--gold-lt);
  line-height:1.1;
}
.page-head-left p{
  margin-top:6px;font-size:14px;
  color:var(--muted);max-width:480px;line-height:1.6;
}
.page-head-right{display:flex;flex-wrap:wrap;gap:10px;align-items:center}

/* ── Button system ──────────────────────────── */
.btn{
  display:inline-flex;align-items:center;justify-content:center;gap:7px;
  padding:9px 18px;border-radius:6px;
  font-family:var(--fu);font-size:13px;font-weight:500;
  text-decoration:none;border:none;cursor:pointer;
  transition:all .18s;white-space:nowrap;
  letter-spacing:.02em;
  min-height:38px;
}
.btn svg{width:14px;height:14px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:1.5}
.btn-primary{
  background:linear-gradient(135deg,var(--gold-lt) 0%,var(--gold) 60%,var(--gold-dk) 100%);
  color:var(--bg);
  box-shadow:0 4px 16px rgba(0,0,0,.3);
}
.btn-primary:hover{filter:brightness(1.07);transform:translateY(-1px);box-shadow:0 7px 22px rgba(0,0,0,.35)}
.btn-primary:active{transform:none}
.btn-ghost{
  background:rgba(200,168,75,.07);
  color:var(--gold);
  border:1px solid rgba(200,168,75,.22);
}
.btn-ghost:hover{background:rgba(200,168,75,.13);border-color:rgba(200,168,75,.38)}
.btn-danger{
  background:rgba(176,42,42,.12);
  color:#fca5a5;
  border:1px solid rgba(176,42,42,.3);
}
.btn-danger:hover{background:rgba(176,42,42,.22);border-color:rgba(176,42,42,.5)}
.btn-sm{padding:7px 13px;font-size:12.5px;min-height:32px}
.btn-sm svg{width:12px;height:12px}
.btn-icon{padding:7px;min-height:32px;width:32px}

/* ── Card ───────────────────────────────────── */
.card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  overflow:hidden;
}

/* ── Stat cards row ─────────────────────────── */
.stat-row{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
  gap:14px;
  margin-bottom:28px;
}
.stat-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  padding:18px 20px;
  position:relative;
  overflow:hidden;
  transition:border-color .2s,transform .18s;
}
.stat-card:hover{border-color:rgba(200,168,75,.25);transform:translateY(-1px)}
.stat-card::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);
  opacity:0;transition:opacity .2s;
}
.stat-card:hover::before{opacity:.6}
.stat-card-icon{
  width:36px;height:36px;
  border-radius:8px;
  background:var(--gold-dim);
  display:flex;align-items:center;justify-content:center;
  margin-bottom:14px;
}
.stat-card-icon svg{width:18px;height:18px;stroke:var(--gold);fill:none;stroke-width:1.5}
.stat-card-num{
  font-family:var(--ff);
  font-size:clamp(26px,3vw,34px);
  font-weight:300;
  color:var(--gold-lt);
  line-height:1;margin-bottom:4px;
}
.stat-card-label{font-size:12px;color:var(--muted);letter-spacing:.04em}

/* ── Flash (session success) ────────────────── */
.flash-success{
  display:flex;align-items:center;gap:10px;
  padding:12px 16px;border-radius:6px;
  background:rgba(34,197,94,.08);
  border:1px solid rgba(34,197,94,.22);
  color:#86efac;font-size:13.5px;
  margin-bottom:20px;
  animation:toastIn .3s ease both;
}
.flash-success svg{width:15px;height:15px;stroke:#86efac;fill:none;stroke-width:1.5;flex-shrink:0}

/* ── Search input ───────────────────────────── */
.search-wrap{position:relative}
.search-wrap svg{
  position:absolute;left:12px;top:50%;transform:translateY(-50%);
  width:15px;height:15px;
  stroke:var(--muted2);fill:none;stroke-width:1.5;
  pointer-events:none;
}
.search-input{
  padding:9px 14px 9px 36px;
  border:1px solid var(--border);
  border-radius:6px;
  background:rgba(0,0,0,.2);
  color:var(--text);
  font-family:var(--fu);
  font-size:13.5px;
  outline:none;
  width:220px;max-width:100%;
  transition:border-color .2s,box-shadow .2s;
}
.search-input:focus{border-color:rgba(200,168,75,.35);box-shadow:0 0 0 3px rgba(200,168,75,.06)}
.search-input::placeholder{color:var(--muted2)}

/* ── Table ──────────────────────────────────── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:600px}
thead th{
  padding:12px 16px;
  text-align:left;
  font-family:var(--fc);
  font-size:9px;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.45);
  background:rgba(0,0,0,.18);
  border-bottom:1px solid var(--border);
  white-space:nowrap;
}
tbody td{
  padding:13px 16px;
  border-bottom:1px solid rgba(200,168,75,.06);
  font-size:13.5px;
  vertical-align:middle;
}
tbody tr{transition:background .12s}
tbody tr:hover{background:rgba(200,168,75,.04)}
tbody tr.hidden{display:none}
tbody tr:last-child td{border-bottom:none}

/* Monospace code */
.mono{font-family:'Courier New',monospace;font-size:12.5px;color:rgba(232,228,217,.7)}

/* Category chip */
.chip{
  display:inline-flex;align-items:center;gap:5px;
  padding:3px 9px;border-radius:999px;
  font-size:11.5px;font-weight:600;
  background:rgba(200,168,75,.1);
  color:var(--gold-lt);
  white-space:nowrap;
}

/* QR thumb */
.qr-thumb{
  width:48px;height:48px;
  object-fit:contain;
  border-radius:6px;
  border:1px solid rgba(200,168,75,.18);
  padding:4px;
  background:#fff;
  transition:transform .18s,box-shadow .18s;
}
.qr-thumb:hover{transform:scale(1.08);box-shadow:0 4px 14px rgba(0,0,0,.3)}

/* Actions cell */
.actions{display:flex;flex-wrap:wrap;gap:6px;align-items:center}

/* Empty state */
.empty-state{
  text-align:center;
  padding:64px 24px;
}
.empty-state svg{
  width:48px;height:48px;
  stroke:rgba(200,168,75,.2);fill:none;stroke-width:1;
  margin:0 auto 16px;
  display:block;
}
.empty-state h3{
  font-family:var(--ff);font-size:20px;font-weight:300;font-style:italic;
  color:rgba(232,228,217,.35);margin-bottom:6px;
}
.empty-state p{font-size:13px;color:var(--muted2)}

/* Pagination */
.pagination{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:20px;flex-wrap:wrap}
.pagination a,.pagination span{
  display:inline-flex;align-items:center;justify-content:center;
  min-width:34px;height:34px;
  padding:0 10px;
  border-radius:6px;
  font-size:13px;
  border:1px solid transparent;
  text-decoration:none;
  color:var(--muted);
  transition:all .15s;
}
.pagination a:hover{border-color:var(--border);color:var(--text)}
.pagination span[aria-current="page"]{
  background:rgba(200,168,75,.14);
  color:var(--gold);
  border-color:rgba(200,168,75,.25);
}

/* ── Form components ────────────────────────── */
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
@media(max-width:700px){.form-grid{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{
  display:block;
  font-family:var(--fc);font-size:9.5px;
  letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.45);
  margin-bottom:8px;
}
.form-input,.form-select,.form-textarea{
  width:100%;
  padding:11px 14px;
  border:1px solid rgba(200,168,75,.16);
  border-radius:6px;
  background:rgba(0,0,0,.2);
  color:var(--text);
  font-family:var(--fu);font-size:14.5px;
  outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  -webkit-appearance:none;
}
.form-input:focus,.form-select:focus,.form-textarea:focus{
  border-color:rgba(200,168,75,.45);
  background:rgba(0,0,0,.28);
  box-shadow:0 0 0 3px rgba(200,168,75,.07);
}
.form-input::placeholder,.form-textarea::placeholder{color:rgba(232,228,217,.18)}
.form-textarea{min-height:100px;resize:vertical;line-height:1.6}
.form-select{
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23c8a84b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 13px center;
  padding-right:36px;cursor:pointer;
}
.form-select option{background:#0e1f10;color:var(--text)}
.form-error{font-size:12px;color:#fca5a5;margin-top:5px}
.form-hint{font-size:12px;color:var(--muted2);margin-top:5px;line-height:1.5}

/* Section title inside form */
.section-title{
  font-family:var(--fc);
  font-size:11px;
  letter-spacing:.14em;
  text-transform:uppercase;
  color:rgba(200,168,75,.5);
  padding-bottom:10px;
  border-bottom:1px solid var(--border);
  margin:28px 0 18px;
  display:flex;align-items:center;gap:10px;
}
.section-title::after{content:'';flex:1;height:1px;background:var(--border)}

/* Translation tab pills */
.tab-pills{
  display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;
}
.tab-pill{
  padding:6px 14px;
  border-radius:999px;
  font-family:var(--fc);font-size:10px;
  letter-spacing:.14em;text-transform:uppercase;
  border:1px solid var(--border);
  background:transparent;
  color:var(--muted);cursor:pointer;
  transition:all .18s;
}
.tab-pill:hover{border-color:rgba(200,168,75,.3);color:var(--text)}
.tab-pill.active{background:rgba(200,168,75,.13);border-color:rgba(200,168,75,.35);color:var(--gold-lt)}

.tab-pane{display:none}
.tab-pane.active{display:grid;gap:16px}

/* Checkbox styled */
.check-wrap{
  display:flex;align-items:center;gap:10px;cursor:pointer;
  font-size:14px;color:var(--muted);
}
.check-wrap input[type=checkbox]{
  width:16px;height:16px;
  appearance:none;-webkit-appearance:none;
  border:1px solid rgba(200,168,75,.25);
  border-radius:4px;background:transparent;
  cursor:pointer;flex-shrink:0;position:relative;
  transition:border-color .2s,background .2s;
}
.check-wrap input[type=checkbox]:checked{background:var(--gold);border-color:var(--gold)}
.check-wrap input[type=checkbox]:checked::after{
  content:'';
  position:absolute;left:4px;top:1px;
  width:5px;height:9px;
  border:1.5px solid #1c1406;
  border-left:none;border-top:none;
  transform:rotate(45deg);
}

/* QR preview box */
.qr-preview-box{
  border:1px dashed rgba(200,168,75,.2);
  border-radius:8px;
  padding:16px;
  background:rgba(200,168,75,.03);
  text-align:center;
}
.qr-preview-img{
  width:150px;height:150px;
  object-fit:contain;
  border-radius:6px;
  border:1px solid rgba(200,168,75,.2);
  padding:8px;
  background:#fff;
  margin:0 auto 10px;
  display:block;
}
.qr-preview-pending{
  height:150px;display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:8px;
  color:var(--muted2);font-size:13px;
}
.qr-preview-pending svg{width:36px;height:36px;stroke:rgba(200,168,75,.18);fill:none;stroke-width:1}

/* 3D badge */
.badge-3d{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 10px;border-radius:999px;
  font-size:11px;font-weight:700;
  letter-spacing:.08em;text-transform:uppercase;
  background:rgba(124,58,237,.15);
  color:#c4b5fd;
  border:1px solid rgba(124,58,237,.25);
}
</style>
@stack('styles')
</head>
<body>

{{-- Mobile topbar --}}
<div class="topbar">
  <div class="topbar-brand">Collections &amp; QR</div>
  <button class="topbar-burger" id="burgerBtn" onclick="toggleSidebar()" aria-label="Menu" type="button">
    <span></span><span></span><span></span>
  </button>
</div>

{{-- Overlay --}}
<div class="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
  <div class="sb-head">
    <div class="sb-logo-row">
      <div class="sb-emblem">
        <img src="{{ asset('images/anp.png') }}" alt="ANP" onerror="this.style.display='none'">
      </div>
      <div class="sb-brand">
        Musée Militaire National
        <span>Administration</span>
      </div>
    </div>
    <div class="sb-arabic">المتحف المركزي للجيش</div>
  </div>

  <nav class="sb-nav">
    <span class="sb-section-label">Menu principal</span>
    <a href="{{ route('admin.dashboard') }}"
       class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 9l-6 6-3-3-4 4"/><path d="M7 7h10v10H7z"/></svg>
      Statistiques
    </a>

    <div class="sb-nav-group">
      <div class="sb-group-title">Artefacts</div>
      <a href="{{ route('admin.artifacts.index') }}"
         class="sb-sublink {{ request()->routeIs('admin.artifacts.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        Fiches &amp; QR
      </a>
      <a href="{{ route('admin.feedbacks.index') }}"
         class="sb-sublink {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M12 17.75l-6.172 3.848 1.639-7.052L2 9.798l7.229-.596L12 2.75l2.771 6.452 7.229.596-5.467 4.948 1.639 7.052z"/></svg>
        Avis visiteurs
      </a>
    </div>

    <span class="sb-section-label" style="margin-top:8px">Navigation</span>
    <a href="{{ url('/') }}" class="sb-link">
      <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Site public
    </a>
  </nav>

  <div class="sb-foot">
    <div class="sb-user">
      <div class="sb-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
      <div class="sb-user-info">
        <div class="sb-user-name">{{ Auth::user()->name }}</div>
        <div class="sb-user-role">Administrateur</div>
      </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sb-logout">
        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Déconnexion
      </button>
    </form>
  </div>
</aside>

{{-- Main content --}}
<div class="main-wrap">
  <div class="main-inner">

    @if(session('success'))
    <div class="flash-success">
      <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
    @endif

    @yield('content')
  </div>
</div>

{{-- Toast container --}}
<div class="toast-container" id="toastContainer"></div>

<script>
/* ── Sidebar toggle ─────────────────────────── */
function toggleSidebar(){document.body.classList.toggle('sidebar-open')}
function closeSidebar(){document.body.classList.remove('sidebar-open')}

/* ── Toast helper ───────────────────────────── */
function showToast(msg,type){
  type=type||'info';
  var c=document.getElementById('toastContainer');
  var t=document.createElement('div');
  t.className='toast '+type;
  t.innerHTML='<div class="toast-dot"></div><span>'+msg+'</span>';
  c.appendChild(t);
  setTimeout(function(){
    t.classList.add('out');
    setTimeout(function(){t.remove()},300);
  },3200);
}

/* Flash → toast on page load */
@if(session('success'))
window.addEventListener('load',function(){showToast('{{ session('success') }}','success')});
@endif
</script>
@stack('scripts')
</body>
</html>
