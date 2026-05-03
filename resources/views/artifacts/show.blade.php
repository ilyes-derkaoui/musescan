{{--
|--------------------------------------------------------------------------
| resources/views/artifacts/show.blade.php
| Page visiteur — fiche artefact (scan QR)
|
| Variables attendues du contrôleur :
|   $artifact        — Artifact model (with relations loaded)
|   $translations    — collection keyed by locale
|   $images          — collection de ArtifactMedia (type=image)
|   $audio           — ArtifactMedia|null (type=audio)
|   $model3d         — ArtifactMedia|null (type=model_3d)
|   $figureArtifacts — collection d'artefacts liés à un personnage (optionnel)
|   $figure          — HistoricalFigure|null
|   $feedbacks       — paginated feedbacks
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="fr" id="htmlRoot">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
<meta name="theme-color" content="#0a1a0c"/>
<title>{{ $artifact->name }} — المتحف المركزي للجيش</title>

{{-- Open Graph --}}
<meta property="og:title" content="{{ $artifact->name }}"/>
<meta property="og:description" content="{{ Str::limit(optional($translations['fr'] ?? null)->description ?? $artifact->name, 160) }}"/>
@if($images->isNotEmpty())
<meta property="og:image" content="{{ asset('storage/'.$images->first()->file_path) }}"/>
@endif

<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet"/>

@if($model3d)
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
@endif

<style>
/* ════════════════════════════════════════════
   RESET & CSS VARIABLES
════════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;height:100%}

:root{
  --bg:#070e08;
  --bg2:#0a1409;
  --panel:#0d1c0f;
  --panel2:#0b180d;
  --border:rgba(200,168,75,.12);
  --gold:#c8a84b;
  --gold-lt:#dfc278;
  --gold-dk:#a8842e;
  --gold-dim:rgba(200,168,75,.12);
  --gold-glow:rgba(200,168,75,.18);
  --ivory:#f3ecd9;
  --ink:#1c1406;
  --text:rgba(243,236,217,.9);
  --muted:rgba(243,236,217,.45);
  --muted2:rgba(243,236,217,.25);
  --hdr:#0e2012;
  --fc:'Cinzel',serif;
  --ff:'Cormorant Garamond',Georgia,serif;
  --fu:'Source Sans 3',system-ui,sans-serif;
  --fa:'Amiri','Traditional Arabic',serif;
  --hdr-h:64px;
  --max:1100px;
}

body{
  font-family:var(--fu);
  background:var(--bg);
  color:var(--text);
  overflow-x:hidden;
  -webkit-font-smoothing:antialiased;
  -webkit-tap-highlight-color:transparent;
}

/* ════════════════════════════════════════════
   HEADER
════════════════════════════════════════════ */
.hdr{
  position:fixed;top:0;left:0;right:0;
  height:var(--hdr-h);z-index:80;
  background:rgba(10,26,12,.92);
  backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 clamp(16px,4vw,40px);
  gap:12px;
}

.hdr-left{display:flex;align-items:center;gap:12px;flex-shrink:0}
.hdr-emblem{
  width:38px;height:38px;border-radius:50%;
  border:1px solid rgba(200,168,75,.3);
  overflow:hidden;background:rgba(200,168,75,.06);
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.hdr-emblem img{width:100%;height:100%;object-fit:contain;padding:4px}
.hdr-title{
  font-family:var(--fc);
  font-size:clamp(9px,1.1vw,11px);
  letter-spacing:.14em;text-transform:uppercase;
  color:var(--gold);line-height:1.3;
}
.hdr-title span{display:block;color:rgba(200,168,75,.4);font-size:.85em;margin-top:1px}

.hdr-right{display:flex;align-items:center;gap:10px}

/* Language switcher */
.lang-wrap{position:relative}
.lang-btn{
  display:inline-flex;align-items:center;gap:7px;
  height:32px;padding:0 11px;
  border-radius:999px;
  border:1px solid rgba(200,168,75,.3);
  background:rgba(8,22,14,.6);
  color:var(--text);
  font-family:var(--fu);font-size:11px;font-weight:600;
  letter-spacing:.12em;text-transform:uppercase;
  cursor:pointer;
  transition:border-color .2s,background .2s;
}
.lang-btn:hover{border-color:rgba(200,168,75,.55);background:rgba(14,35,22,.8)}
.lang-btn svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2;opacity:.6}

.lang-sheet{
  position:absolute;top:calc(100% + 8px);right:0;
  min-width:200px;padding:6px;
  border-radius:10px;
  border:1px solid rgba(200,168,75,.2);
  background:rgba(8,22,14,.98);
  backdrop-filter:blur(12px);
  box-shadow:0 16px 40px rgba(0,0,0,.5);
  display:none;z-index:90;
}
.lang-wrap.open .lang-sheet{display:block}

.lang-row{
  display:flex;align-items:center;gap:9px;
  width:100%;border:none;border-radius:7px;
  padding:8px 11px;background:transparent;
  color:rgba(255,255,255,.8);
  font-family:var(--fu);font-size:13px;
  text-align:left;cursor:pointer;
  transition:background .15s,color .15s;
}
.lang-row:hover{background:rgba(200,168,75,.08);color:var(--gold-lt)}
.lang-row.active{background:rgba(200,168,75,.12);color:var(--gold)}
.lang-flag{font-size:16px;width:22px;text-align:center;line-height:1}
.lang-name{flex:1}
.lang-code{font-size:10px;font-weight:700;letter-spacing:.1em;color:rgba(200,168,75,.45);font-family:var(--fc)}

/* Back to home */
.hdr-back{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 14px;border-radius:999px;
  border:1px solid var(--border);background:transparent;
  color:var(--muted);font-size:12px;
  text-decoration:none;
  transition:border-color .2s,color .2s;
  white-space:nowrap;
}
.hdr-back:hover{border-color:rgba(200,168,75,.35);color:var(--gold-lt)}
.hdr-back svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:1.5}

/* ════════════════════════════════════════════
   HERO / CINEMATIC OPENER
════════════════════════════════════════════ */
.artifact-hero{
  position:relative;
  min-height:60vh;
  display:flex;align-items:flex-end;
  padding-top:var(--hdr-h);
  overflow:hidden;
}

.hero-bg-img{
  position:absolute;inset:0;
  background-size:cover;background-position:center;
  filter:brightness(.3) saturate(.7);
  transform:scale(1.06);
  transition:transform 8s ease-out;
}
.hero-bg-img.loaded{transform:scale(1)}

.hero-bg-gradient{
  position:absolute;inset:0;
  background:linear-gradient(
    to bottom,
    rgba(7,14,8,.2) 0%,
    rgba(7,14,8,.05) 30%,
    rgba(7,14,8,.85) 75%,
    rgba(7,14,8,1) 100%
  );
}

/* Grain */
.hero-grain{
  position:absolute;inset:0;
  opacity:.035;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size:180px;pointer-events:none;
}

.hero-content{
  position:relative;z-index:2;
  width:100%;max-width:var(--max);
  margin:0 auto;
  padding:clamp(40px,6vw,80px) clamp(20px,5vw,60px) clamp(28px,4vw,48px);
  animation:heroUp .9s ease both;
}

@keyframes heroUp{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:translateY(0)}
}

.hero-meta{
  display:flex;align-items:center;flex-wrap:wrap;gap:10px;
  margin-bottom:18px;
}
.hero-breadcrumb{
  font-family:var(--fc);font-size:10px;
  letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.5);
  display:flex;align-items:center;gap:6px;
}
.hero-breadcrumb svg{width:10px;height:10px;stroke:currentColor;fill:none;stroke-width:2;opacity:.5}
.hero-chip{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 10px;border-radius:999px;
  font-size:11px;font-weight:600;
  background:rgba(200,168,75,.1);
  border:1px solid rgba(200,168,75,.22);
  color:var(--gold-lt);
  font-family:var(--fc);letter-spacing:.08em;
}
.hero-floor-chip{
  padding:4px 10px;border-radius:999px;
  font-size:11px;font-weight:600;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.12);
  color:var(--muted);
  font-family:var(--fc);letter-spacing:.06em;
}

/* Divider gold */
.dg{display:flex;align-items:center;gap:10px;width:fit-content;margin-bottom:14px}
.dg-l{width:40px;height:1px;background:linear-gradient(90deg,transparent,var(--gold))}
.dg-d{width:6px;height:6px;background:var(--gold);transform:rotate(45deg);flex-shrink:0}

.hero-title-fr{
  font-family:var(--ff);
  font-size:clamp(32px,6vw,72px);
  font-weight:300;font-style:italic;
  color:var(--ivory);
  line-height:1.08;
  margin-bottom:6px;
}

.hero-title-ar{
  font-family:var(--fa);
  font-size:clamp(20px,3vw,34px);
  font-weight:400;
  color:rgba(200,168,75,.5);
  direction:rtl;margin-bottom:18px;
}

.hero-stats{
  display:flex;flex-wrap:wrap;gap:20px;margin-top:6px;
}
.hero-stat{
  display:flex;align-items:center;gap:7px;
  font-size:12.5px;color:var(--muted);
}
.hero-stat svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:1.5;opacity:.6}

/* ════════════════════════════════════════════
   MAIN CONTENT WRAPPER
════════════════════════════════════════════ */
.artifact-body{
  max-width:var(--max);
  margin:0 auto;
  padding:0 clamp(20px,5vw,60px) 80px;
}

/* ════════════════════════════════════════════
   GALLERY — WOW EFFECT
════════════════════════════════════════════ */
.gallery-section{
  margin-bottom:64px;
}

.gallery-grid{
  display:grid;
  gap:3px;
}

/* 1 image */
.gallery-grid.count-1{grid-template-columns:1fr}
.gallery-grid.count-1 .gallery-item{aspect-ratio:16/9}

/* 2 images */
.gallery-grid.count-2{grid-template-columns:1fr 1fr}
.gallery-grid.count-2 .gallery-item{aspect-ratio:4/3}

/* 3 images */
.gallery-grid.count-3{
  grid-template-columns:2fr 1fr;
  grid-template-rows:repeat(2,200px);
}
.gallery-grid.count-3 .gallery-item:first-child{grid-row:span 2;aspect-ratio:unset}
.gallery-grid.count-3 .gallery-item{aspect-ratio:unset;height:200px}

/* 4+ images */
.gallery-grid.count-4,.gallery-grid.count-more{
  grid-template-columns:2fr 1fr 1fr;
  grid-template-rows:260px 200px;
}
.gallery-grid.count-4 .gallery-item:first-child,
.gallery-grid.count-more .gallery-item:first-child{
  grid-row:span 2;
  grid-column:1;
  aspect-ratio:unset;
}
.gallery-grid.count-4 .gallery-item,
.gallery-grid.count-more .gallery-item{height:200px}
.gallery-grid.count-more .gallery-item:nth-child(6){position:relative}
.gallery-grid.count-more .gallery-item:nth-child(n+7){display:none}

/* Gallery item */
.gallery-item{
  position:relative;overflow:hidden;
  cursor:pointer;
  background:rgba(0,0,0,.3);
  border-radius:0;
}
.gallery-item:first-child{border-radius:10px 0 0 10px}
.gallery-item:last-child{border-radius:0 10px 10px 0}

.gallery-item img{
  width:100%;height:100%;
  object-fit:cover;
  display:block;
  transition:transform .6s cubic-bezier(.25,.46,.45,.94),filter .4s;
  filter:brightness(.92) saturate(.9);
}
.gallery-item:hover img{
  transform:scale(1.06);
  filter:brightness(1) saturate(1.1);
}

/* Overlay on hover */
.gallery-item::after{
  content:'';
  position:absolute;inset:0;
  background:rgba(200,168,75,.0);
  transition:background .3s;
  pointer-events:none;
}
.gallery-item:hover::after{background:rgba(200,168,75,.08)}

/* "More" badge */
.gallery-more-badge{
  position:absolute;inset:0;
  display:flex;align-items:center;justify-content:center;
  background:rgba(7,14,8,.72);
  backdrop-filter:blur(3px);
  font-family:var(--ff);font-size:28px;font-weight:300;font-style:italic;
  color:var(--ivory);
  pointer-events:none;
}

/* Zoom icon */
.gallery-zoom{
  position:absolute;top:12px;right:12px;
  width:32px;height:32px;
  border-radius:6px;
  background:rgba(7,14,8,.6);
  border:1px solid rgba(200,168,75,.2);
  display:flex;align-items:center;justify-content:center;
  opacity:0;transform:scale(.8);
  transition:opacity .25s,transform .25s;
  pointer-events:none;
}
.gallery-zoom svg{width:14px;height:14px;stroke:var(--gold-lt);fill:none;stroke-width:1.5}
.gallery-item:hover .gallery-zoom{opacity:1;transform:scale(1)}

/* ─── Mobile gallery (horizontal scroll) ── */
@media(max-width:660px){
  .gallery-grid{
    display:flex;flex-direction:row;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    gap:8px;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:none;
    border-radius:10px;
  }
  .gallery-grid::-webkit-scrollbar{display:none}
  .gallery-grid .gallery-item{
    flex:0 0 85vw;height:56vw;border-radius:10px!important;
    scroll-snap-align:start;
  }
  .gallery-grid .gallery-item img{height:100%}
}

/* ════════════════════════════════════════════
   LIGHTBOX (FULL-SCREEN)
════════════════════════════════════════════ */
.lightbox{
  position:fixed;inset:0;z-index:200;
  display:none;align-items:center;justify-content:center;
  background:rgba(0,0,0,.94);
  backdrop-filter:blur(12px);
  padding:20px;
}
.lightbox.open{display:flex}

.lb-img-wrap{
  position:relative;
  max-width:90vw;max-height:88vh;
  animation:lbIn .28s ease both;
}
@keyframes lbIn{
  from{opacity:0;transform:scale(.94)}
  to{opacity:1;transform:scale(1)}
}
.lb-img{
  max-width:90vw;max-height:84vh;
  object-fit:contain;
  border-radius:6px;
  border:1px solid rgba(200,168,75,.15);
  display:block;
}
.lb-close{
  position:fixed;top:20px;right:20px;
  width:40px;height:40px;
  background:rgba(200,168,75,.12);
  border:1px solid rgba(200,168,75,.25);
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--gold-lt);font-size:20px;
  transition:background .2s;z-index:201;
}
.lb-close:hover{background:rgba(200,168,75,.22)}
.lb-nav{
  position:fixed;top:50%;transform:translateY(-50%);
  width:44px;height:44px;
  background:rgba(200,168,75,.1);
  border:1px solid rgba(200,168,75,.22);
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--gold-lt);
  transition:background .2s,opacity .2s;z-index:201;
}
.lb-nav:hover{background:rgba(200,168,75,.2)}
.lb-nav.prev{left:16px}
.lb-nav.next{right:16px}
.lb-nav svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2}
.lb-nav.disabled{opacity:.2;pointer-events:none}
.lb-caption{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
  font-family:var(--ff);font-size:14px;font-style:italic;
  color:rgba(243,236,217,.5);
  text-align:center;z-index:201;
  white-space:nowrap;
}
.lb-counter{
  position:fixed;top:24px;left:50%;transform:translateX(-50%);
  font-family:var(--fc);font-size:10px;
  letter-spacing:.2em;color:rgba(200,168,75,.45);
  z-index:201;
}
.lb-dots{
  position:fixed;bottom:56px;left:50%;transform:translateX(-50%);
  display:flex;gap:6px;z-index:201;
}
.lb-dot{
  width:6px;height:6px;border-radius:50%;
  background:rgba(200,168,75,.2);
  cursor:pointer;transition:background .2s,transform .2s;
}
.lb-dot.active{background:var(--gold);transform:scale(1.3)}

/* ════════════════════════════════════════════
   CONTENT GRID (description + sidebar)
════════════════════════════════════════════ */
.content-grid{
  display:grid;
  grid-template-columns:1fr 320px;
  gap:32px;
  align-items:start;
}
@media(max-width:900px){
  .content-grid{grid-template-columns:1fr}
}

/* ════════════════════════════════════════════
   DESCRIPTION SECTION
════════════════════════════════════════════ */
.desc-section{margin-bottom:48px}

.section-label{
  display:flex;align-items:center;gap:10px;
  font-family:var(--fc);font-size:9.5px;
  letter-spacing:.22em;text-transform:uppercase;
  color:rgba(200,168,75,.4);
  margin-bottom:18px;
}
.section-label::after{content:'';flex:1;height:1px;background:rgba(200,168,75,.1)}

.desc-text{
  font-family:var(--ff);
  font-size:clamp(16px,1.8vw,19px);
  font-weight:300;
  line-height:1.9;
  color:rgba(243,236,217,.75);
}
.desc-text.rtl{direction:rtl;text-align:right;font-family:var(--fa);font-size:clamp(16px,2vw,20px)}

/* Read more toggle */
.desc-wrap{position:relative;overflow:hidden}
.desc-wrap.collapsed{max-height:260px}
.desc-fade{
  position:absolute;bottom:0;left:0;right:0;height:100px;
  background:linear-gradient(to top,var(--bg),transparent);
  pointer-events:none;display:none;
}
.desc-wrap.collapsed .desc-fade{display:block}
.desc-toggle{
  display:inline-flex;align-items:center;gap:7px;
  margin-top:14px;
  font-size:13px;color:var(--gold);
  background:none;border:none;cursor:pointer;
  font-family:var(--fu);
  transition:color .2s;
}
.desc-toggle:hover{color:var(--gold-lt)}
.desc-toggle svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;transition:transform .3s}
.desc-toggle.open svg{transform:rotate(180deg)}

/* ════════════════════════════════════════════
   AUDIO PLAYER
════════════════════════════════════════════ */
.audio-section{
  background:linear-gradient(135deg,rgba(200,168,75,.08),rgba(200,168,75,.04));
  border:1px solid rgba(200,168,75,.18);
  border-radius:12px;
  padding:20px 22px;
  margin-bottom:36px;
}
.audio-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:16px;
}
.audio-title{
  font-family:var(--fc);font-size:10px;
  letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.55);
  display:flex;align-items:center;gap:8px;
}
.audio-lang-tag{
  font-size:9px;padding:2px 7px;border-radius:999px;
  background:rgba(200,168,75,.1);color:var(--gold-lt);
  letter-spacing:.1em;
}
.audio-bar{
  display:flex;align-items:center;gap:14px;
}
.audio-play{
  width:44px;height:44px;border-radius:50%;
  background:linear-gradient(135deg,var(--gold-lt),var(--gold));
  border:none;cursor:pointer;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 4px 16px rgba(200,168,75,.25);
  transition:transform .2s,box-shadow .2s;
}
.audio-play:hover{transform:scale(1.06);box-shadow:0 6px 22px rgba(200,168,75,.35)}
.audio-play svg{width:16px;height:16px;fill:var(--ink);stroke:none;margin-left:2px}
.audio-play.playing .play-icon{display:none}
.audio-play.playing .pause-icon{display:block!important}

.audio-progress-wrap{flex:1}
.audio-timeline{
  position:relative;
  height:4px;background:rgba(200,168,75,.15);
  border-radius:4px;cursor:pointer;margin-bottom:8px;
  overflow:visible;
}
.audio-progress-bar{
  height:100%;background:linear-gradient(90deg,var(--gold-lt),var(--gold));
  border-radius:4px;width:0%;
  transition:width .1s;
  position:relative;
}
.audio-progress-bar::after{
  content:'';
  position:absolute;right:-5px;top:50%;transform:translateY(-50%);
  width:10px;height:10px;border-radius:50%;
  background:var(--gold-lt);
  box-shadow:0 0 8px rgba(200,168,75,.5);
}
.audio-time{
  display:flex;justify-content:space-between;
  font-family:'Courier New',monospace;
  font-size:10.5px;color:var(--muted2);
}
.audio-wave{
  display:flex;align-items:center;gap:2px;height:24px;
}
.audio-wave span{
  width:2px;background:rgba(200,168,75,.25);border-radius:2px;
  animation:wave 1.2s ease infinite;
  flex-shrink:0;
}
.audio-wave span:nth-child(1){height:6px;animation-delay:0s}
.audio-wave span:nth-child(2){height:14px;animation-delay:.1s}
.audio-wave span:nth-child(3){height:20px;animation-delay:.2s}
.audio-wave span:nth-child(4){height:12px;animation-delay:.3s}
.audio-wave span:nth-child(5){height:18px;animation-delay:.15s}
.audio-wave span:nth-child(6){height:8px;animation-delay:.25s}
.audio-wave span:nth-child(7){height:16px;animation-delay:.05s}
@keyframes wave{
  0%,100%{opacity:.25}
  50%{opacity:.7;transform:scaleY(1.2)}
}
.audio-wave.playing span{animation-play-state:running;background:rgba(200,168,75,.5)}
.audio-wave.paused span{animation-play-state:paused}

/* ════════════════════════════════════════════
   3D MODEL VIEWER
════════════════════════════════════════════ */
.model-section{
  margin-bottom:48px;
}
.model-viewer-wrap{
  position:relative;
  background:radial-gradient(ellipse at center,rgba(200,168,75,.06),rgba(7,14,8,.5)),
             var(--panel);
  border:1px solid rgba(200,168,75,.18);
  border-radius:14px;overflow:hidden;
  height:420px;
}
@media(max-width:600px){.model-viewer-wrap{height:280px}}

model-viewer{
  width:100%;height:100%;
  --poster-color:transparent;
}

.model-hint{
  position:absolute;bottom:14px;left:50%;transform:translateX(-50%);
  display:flex;align-items:center;gap:7px;
  padding:7px 14px;border-radius:999px;
  background:rgba(7,14,8,.7);border:1px solid rgba(200,168,75,.18);
  font-size:11px;color:var(--muted);
  backdrop-filter:blur(6px);
  pointer-events:none;white-space:nowrap;
}
.model-hint svg{width:12px;height:12px;stroke:var(--gold);fill:none;stroke-width:1.5}

.model-badge{
  position:absolute;top:14px;left:14px;
  display:inline-flex;align-items:center;gap:5px;
  padding:5px 11px;border-radius:999px;
  background:rgba(124,58,237,.15);
  border:1px solid rgba(124,58,237,.3);
  font-family:var(--fc);font-size:9px;
  letter-spacing:.15em;text-transform:uppercase;
  color:#c4b5fd;
}
.model-badge::before{
  content:'';width:6px;height:6px;border-radius:50%;
  background:#c4b5fd;animation:pulse3d 1.8s ease infinite;
}
@keyframes pulse3d{
  0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(196,181,253,.4)}
  50%{opacity:.6;box-shadow:0 0 0 5px rgba(196,181,253,0)}
}

/* ════════════════════════════════════════════
   SIDEBAR
════════════════════════════════════════════ */
.sidebar-sticky{
  position:sticky;top:calc(var(--hdr-h) + 20px);
  display:flex;flex-direction:column;gap:16px;
}
@media(max-width:900px){.sidebar-sticky{position:static}}

.info-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:12px;
  overflow:hidden;
}
.info-card-head{
  padding:14px 18px;
  border-bottom:1px solid var(--border);
  font-family:var(--fc);font-size:9.5px;
  letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.4);
  display:flex;align-items:center;gap:8px;
}
.info-card-head svg{width:13px;height:13px;stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0;opacity:.7}
.info-card-body{padding:18px}

.info-row{
  display:flex;flex-direction:column;gap:3px;
  padding:10px 0;
  border-bottom:1px solid rgba(200,168,75,.06);
}
.info-row:last-child{border-bottom:none;padding-bottom:0}
.info-row:first-child{padding-top:0}
.info-label{font-size:10.5px;color:var(--muted2);font-family:var(--fc);letter-spacing:.1em;text-transform:uppercase}
.info-val{font-size:14px;color:var(--text);font-weight:500}

/* QR Code card */
.qr-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:12px;
  padding:18px;
  text-align:center;
}
.qr-card-img{
  width:130px;height:130px;
  object-fit:contain;
  background:#fff;
  border-radius:8px;
  border:1px solid rgba(200,168,75,.15);
  padding:8px;
  margin:0 auto 12px;
  display:block;
  transition:transform .3s;
}
.qr-card-img:hover{transform:scale(1.04)}
.qr-card-label{
  font-family:var(--fc);font-size:9.5px;
  letter-spacing:.16em;text-transform:uppercase;
  color:rgba(200,168,75,.4);
  margin-bottom:4px;
}
.qr-card-code{
  font-family:'Courier New',monospace;font-size:11.5px;
  color:var(--muted);
}

/* ════════════════════════════════════════════
   HISTORICAL FIGURE SECTION
════════════════════════════════════════════ */
.figure-section{
  background:linear-gradient(135deg,rgba(200,168,75,.06),rgba(200,168,75,.02));
  border:1px solid rgba(200,168,75,.14);
  border-radius:12px;
  padding:22px;
  margin-bottom:40px;
}
.figure-header{
  display:flex;align-items:center;gap:14px;
  margin-bottom:20px;
}
.figure-avatar{
  width:56px;height:56px;border-radius:50%;
  background:linear-gradient(135deg,var(--gold-dk),var(--gold));
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fa);font-size:22px;color:var(--ink);
  flex-shrink:0;
  border:2px solid rgba(200,168,75,.4);
}
.figure-name{
  font-family:var(--fa);font-size:20px;
  color:var(--ivory);direction:rtl;margin-bottom:2px;
}
.figure-name-fr{
  font-family:var(--ff);font-size:14px;font-style:italic;
  color:var(--muted);
}
.figure-related-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(120px,1fr));
  gap:10px;
}
.figure-related-item{
  display:block;text-decoration:none;
  background:rgba(0,0,0,.2);
  border:1px solid var(--border);
  border-radius:8px;overflow:hidden;
  transition:border-color .2s,transform .18s;
}
.figure-related-item:hover{border-color:rgba(200,168,75,.3);transform:translateY(-2px)}
.figure-related-img{
  width:100%;aspect-ratio:1;
  object-fit:cover;
  filter:brightness(.8) saturate(.7);
  transition:filter .3s;
}
.figure-related-item:hover .figure-related-img{filter:brightness(.95) saturate(.9)}
.figure-related-no-img{
  width:100%;aspect-ratio:1;
  background:var(--panel2);
  display:flex;align-items:center;justify-content:center;
}
.figure-related-no-img svg{width:28px;height:28px;stroke:rgba(200,168,75,.2);fill:none;stroke-width:1}
.figure-related-name{
  padding:7px 9px;
  font-size:11.5px;color:var(--muted);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}

/* ════════════════════════════════════════════
   FEEDBACK SECTION
════════════════════════════════════════════ */
.feedback-section{margin-top:56px}

.feedback-stats{
  display:flex;align-items:center;gap:24px;
  margin-bottom:28px;flex-wrap:wrap;
}
.feedback-avg{
  font-family:var(--ff);
  font-size:clamp(36px,5vw,54px);font-weight:300;
  color:var(--gold-lt);line-height:1;
}
.feedback-stars{display:flex;gap:4px;margin-top:4px}
.star-full{color:var(--gold);font-size:18px}
.star-half{color:var(--gold);font-size:18px;opacity:.5}
.star-empty{color:rgba(200,168,75,.2);font-size:18px}
.feedback-count{font-size:12px;color:var(--muted2);margin-top:4px}

/* Individual feedback card */
.fb-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  padding:18px 20px;
  margin-bottom:12px;
  transition:border-color .2s;
}
.fb-card:hover{border-color:rgba(200,168,75,.2)}
.fb-head{
  display:flex;align-items:flex-start;
  justify-content:space-between;gap:12px;
  margin-bottom:10px;
}
.fb-author{display:flex;align-items:center;gap:10px}
.fb-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,rgba(200,168,75,.2),rgba(200,168,75,.08));
  border:1px solid rgba(200,168,75,.18);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--fc);font-size:12px;color:var(--gold-lt);
  flex-shrink:0;font-weight:600;
}
.fb-name{font-size:14px;font-weight:500;color:var(--text)}
.fb-date{font-size:11.5px;color:var(--muted2)}
.fb-stars{color:var(--gold);font-size:14px;letter-spacing:.04em}
.fb-comment{
  font-family:var(--ff);font-size:15px;font-style:italic;
  color:rgba(243,236,217,.6);line-height:1.75;
}

/* Feedback form */
.fb-form{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:12px;
  padding:24px;
  margin-top:20px;
}
.fb-form-title{
  font-family:var(--ff);font-size:20px;font-weight:300;font-style:italic;
  color:var(--gold-lt);margin-bottom:18px;
}

/* Star rating interactive */
.star-input{
  display:flex;gap:6px;margin-bottom:18px;
  flex-direction:row-reverse;justify-content:flex-end;
}
.star-input input[type=radio]{display:none}
.star-input label{
  font-size:28px;cursor:pointer;
  color:rgba(200,168,75,.2);
  transition:color .15s,transform .15s;
  line-height:1;
}
.star-input label:hover,.star-input label:hover~label{color:var(--gold)}
.star-input input:checked~label{color:var(--gold)}
.star-input label:hover{transform:scale(1.15)}

.fb-input,.fb-textarea{
  width:100%;padding:11px 14px;
  border:1px solid rgba(200,168,75,.14);
  border-radius:6px;background:rgba(0,0,0,.2);
  color:var(--text);font-family:var(--fu);font-size:14.5px;
  outline:none;
  transition:border-color .2s,box-shadow .2s;
  margin-bottom:14px;
}
.fb-input:focus,.fb-textarea:focus{
  border-color:rgba(200,168,75,.4);
  box-shadow:0 0 0 3px rgba(200,168,75,.06);
}
.fb-input::placeholder,.fb-textarea::placeholder{color:rgba(243,236,217,.18)}
.fb-textarea{min-height:90px;resize:vertical;line-height:1.6}

.fb-submit{
  display:inline-flex;align-items:center;gap:9px;
  padding:12px 28px;
  font-family:var(--fc);font-size:11px;font-weight:600;
  letter-spacing:.2em;text-transform:uppercase;
  color:var(--ink);
  background:linear-gradient(135deg,var(--gold-lt),var(--gold));
  border:none;border-radius:6px;cursor:pointer;
  box-shadow:0 4px 16px rgba(0,0,0,.3);
  transition:filter .2s,transform .15s;
}
.fb-submit:hover{filter:brightness(1.07);transform:translateY(-1px)}
.fb-submit svg{width:14px;height:14px;fill:currentColor}

/* ════════════════════════════════════════════
   REVEAL ANIMATIONS (Intersection Observer)
════════════════════════════════════════════ */
.reveal{
  opacity:0;transform:translateY(28px);
  transition:opacity .7s ease,transform .7s ease;
}
.reveal.visible{opacity:1;transform:none}
.reveal-delay-1{transition-delay:.1s}
.reveal-delay-2{transition-delay:.2s}
.reveal-delay-3{transition-delay:.3s}

/* ════════════════════════════════════════════
   FOOTER
════════════════════════════════════════════ */
.footer{
  background:var(--hdr);
  border-top:1px solid var(--border);
  padding:32px 40px;text-align:center;
}
.footer-deco{
  display:flex;align-items:center;justify-content:center;
  gap:12px;margin-bottom:16px;
}
.fd-l{width:50px;height:1px}
.fd-l.l{background:linear-gradient(90deg,transparent,var(--gold))}
.fd-l.r{background:linear-gradient(90deg,var(--gold),transparent)}
.fd-d{width:6px;height:6px;background:var(--gold);transform:rotate(45deg);flex-shrink:0}
.footer-name{font-family:var(--fa);font-size:16px;color:rgba(200,168,75,.5);direction:rtl;margin-bottom:3px}
.footer-copy{font-family:var(--fc);font-size:9.5px;letter-spacing:.18em;text-transform:uppercase;color:rgba(200,168,75,.22)}

/* ════════════════════════════════════════════
   RESPONSIVE UTILS
════════════════════════════════════════════ */
@media(max-width:600px){
  .hdr-back span{display:none}
  .artifact-hero{min-height:50vh}
  .hero-title-ar{display:none}
  .content-grid{gap:24px}
  .gallery-section{margin-bottom:40px}
  .model-viewer-wrap{height:260px}
  .feedback-stats{gap:14px}
}
</style>
</head>
<body>

{{-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ --}}
<header class="hdr">
  <div class="hdr-left">
    <div class="hdr-emblem">
      <img src="{{ asset('images/anp.png') }}" alt="ANP"
           onerror="this.style.display='none'"/>
    </div>
    <div class="hdr-title">
      Musée Militaire National
      <span>المتحف المركزي للجيش</span>
    </div>
  </div>

  <div class="hdr-right">
    <a class="hdr-back" href="{{ url('/') }}">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      <span>Accueil</span>
    </a>

    {{-- Language switcher --}}
    <div class="lang-wrap" id="langWrap">
      <button class="lang-btn" onclick="toggleLang()" type="button" aria-label="Changer la langue">
        <span id="langCurrent">FR</span>
        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="lang-sheet" id="langSheet">
        @foreach([
          ['fr','🇫🇷','Français'],
          ['ar','🇩🇿','العربية'],
          ['en','🇬🇧','English'],
          ['es','🇪🇸','Español'],
          ['zh','🇨🇳','中文'],
          ['ru','🇷🇺','Русский'],
        ] as [$loc,$flag,$label])
        <button class="lang-row {{ $loop->first ? 'active' : '' }}"
                data-locale="{{ $loc }}"
                onclick="switchLang('{{ $loc }}',this)"
                type="button">
          <span class="lang-flag">{{ $flag }}</span>
          <span class="lang-name">{{ $label }}</span>
          <span class="lang-code">{{ strtoupper($loc) }}</span>
        </button>
        @endforeach
      </div>
    </div>
  </div>
</header>


{{-- ══════════════════════════════════════
     HERO — CINEMATIC
══════════════════════════════════════ --}}
<section class="artifact-hero">

  {{-- Background = première image de la galerie --}}
  @if($images->isNotEmpty())
  <div class="hero-bg-img" id="heroBg"
       style="background-image:url('{{ asset('storage/'.$images->first()->file_path) }}')">
  </div>
  @else
  <div class="hero-bg-img" style="background:var(--panel2)"></div>
  @endif

  <div class="hero-bg-gradient"></div>
  <div class="hero-grain"></div>

  <div class="hero-content">

    {{-- Breadcrumb + meta --}}
    <div class="hero-meta">
      <div class="hero-breadcrumb">
        <span>Musée</span>
        <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>{{ $artifact->category?->name ?? 'Collection' }}</span>
      </div>
      @if($artifact->category)
      <span class="hero-chip">{{ $artifact->category->name }}</span>
      @endif
      <span class="hero-floor-chip">Étage {{ $artifact->floor }}</span>
    </div>

    {{-- Gold divider --}}
    <div class="dg">
      <div class="dg-l"></div>
      <div class="dg-d"></div>
    </div>

    {{-- Title (multilang) --}}
    <h1 class="hero-title-fr" id="titleFr">
      {{ optional($translations['fr'] ?? null)->name ?? $artifact->name }}
    </h1>
    <h1 class="hero-title-fr" id="titleEn" style="display:none">
      {{ optional($translations['en'] ?? null)->name ?? $artifact->name }}
    </h1>
    <h1 class="hero-title-fr" id="titleEs" style="display:none">
      {{ optional($translations['es'] ?? null)->name ?? $artifact->name }}
    </h1>
    <h1 class="hero-title-fr" id="titleZh" style="display:none">
      {{ optional($translations['zh'] ?? null)->name ?? $artifact->name }}
    </h1>
    <h1 class="hero-title-fr" id="titleRu" style="display:none">
      {{ optional($translations['ru'] ?? null)->name ?? $artifact->name }}
    </h1>
    <h1 class="hero-title-fr" id="titleAr" style="display:none;direction:rtl;font-family:var(--fa);color:var(--ivory)">
      {{ optional($translations['ar'] ?? null)->name ?? $artifact->name }}
    </h1>

    @if(optional($translations['ar'] ?? null)->name)
    <p class="hero-title-ar">{{ optional($translations['ar'] ?? null)->name }}</p>
    @endif

    <div class="hero-stats">
      @if($artifact->year ?? null)
      <div class="hero-stat">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $artifact->year }}
      </div>
      @endif
      <div class="hero-stat">
        <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Étage {{ $artifact->floor }}{{ $artifact->room ? ', '.$artifact->room : '' }}
      </div>
      <div class="hero-stat">
        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ $artifact->visits_count ?? $artifact->visits()->count() }} vues
      </div>
    </div>

  </div>
</section>


{{-- ══════════════════════════════════════
     BODY
══════════════════════════════════════ --}}
<div class="artifact-body">

  {{-- ── GALLERY ─────────────────────────── --}}
  @if($images->isNotEmpty())
  <div class="gallery-section reveal">

    @php
      $cnt = $images->count();
      $cls = $cnt === 1 ? 'count-1' : ($cnt === 2 ? 'count-2' : ($cnt === 3 ? 'count-3' : ($cnt === 4 ? 'count-4' : 'count-more')));
    @endphp

    <div class="gallery-grid {{ $cls }}" id="galleryGrid">
      @foreach($images as $i => $img)
      <div class="gallery-item" onclick="openLightbox({{ $i }})" role="button" tabindex="0"
           aria-label="Voir l'image {{ $i+1 }}">
        <img src="{{ asset('storage/'.$img->file_path) }}"
             alt="{{ $artifact->name }} — image {{ $i+1 }}"
             loading="{{ $i === 0 ? 'eager' : 'lazy' }}"/>
        <div class="gallery-zoom">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/><path d="M11 8v6M8 11h6"/></svg>
        </div>
        @if($i === 5 && $cnt > 6)
        <div class="gallery-more-badge">+{{ $cnt - 5 }}</div>
        @endif
      </div>
      @endforeach
    </div>

  </div>
  @endif


  {{-- ── CONTENT GRID ──────────────────── --}}
  <div class="content-grid">

    {{-- LEFT : Description + audio + 3D + figure ── --}}
    <div>

      {{-- ── AUDIO PLAYER ─────────────────── --}}
      @if($audio)
      <div class="audio-section reveal">
        <div class="audio-header">
          <div class="audio-title">
            <svg width="13" height="13" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0">
              <path d="M9 18V5l12-2v13"/>
              <circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>
            </svg>
            Narration audio
            <span class="audio-lang-tag">EN</span>
          </div>
          <div class="audio-wave" id="audioWave">
            <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
          </div>
        </div>
        <div class="audio-bar">
          <button class="audio-play" id="audioPlayBtn" onclick="toggleAudio()" type="button" aria-label="Lire la narration">
            <svg class="play-icon" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3" fill="currentColor"/></svg>
            <svg class="pause-icon" viewBox="0 0 24 24" style="display:none"><rect x="6" y="4" width="4" height="16" fill="currentColor"/><rect x="14" y="4" width="4" height="16" fill="currentColor"/></svg>
          </button>
          <div class="audio-progress-wrap">
            <div class="audio-timeline" id="audioTimeline" onclick="seekAudio(event)">
              <div class="audio-progress-bar" id="audioBar"></div>
            </div>
            <div class="audio-time">
              <span id="audioCurrentTime">0:00</span>
              <span id="audioDuration">—</span>
            </div>
          </div>
        </div>
        <audio id="audioEl" preload="metadata" src="{{ asset('storage/'.$audio->file_path) }}"></audio>
      </div>
      @endif


      {{-- ── DESCRIPTION ──────────────────── --}}
      <div class="desc-section reveal reveal-delay-1">
        <div class="section-label">Description historique</div>

        {{-- French (default) --}}
        @foreach(['fr','ar','en','es','zh','ru'] as $loc)
        @if($translation = ($translations[$loc] ?? null))
        <div id="desc-{{ $loc }}" class="{{ $loc !== 'fr' ? 'lang-block' : '' }}"
             data-locale="{{ $loc }}"
             style="{{ $loc !== 'fr' ? 'display:none' : '' }}">
          <div class="desc-wrap {{ strlen($translation->description) > 600 ? 'collapsed' : '' }}"
               id="descWrap-{{ $loc }}">
            <p class="desc-text {{ $loc === 'ar' ? 'rtl' : '' }}">
              {!! nl2br(e($translation->description)) !!}
            </p>
            @if(strlen($translation->description) > 600)
            <div class="desc-fade"></div>
            @endif
          </div>
          @if(strlen($translation->description) > 600)
          <button class="desc-toggle" id="toggle-{{ $loc }}" onclick="toggleDesc('{{ $loc }}')" type="button">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            Lire la suite
          </button>
          @endif
        </div>
        @endif
        @endforeach

        {{-- Fallback if no translation --}}
        @if($translations->isEmpty())
        <p class="desc-text" style="color:var(--muted)">Aucune description disponible pour cet artefact.</p>
        @endif
      </div>


      {{-- ── 3D MODEL ──────────────────────── --}}
      @if($model3d)
      <div class="model-section reveal reveal-delay-2">
        <div class="section-label">Modèle 3D interactif</div>
        <div class="model-viewer-wrap">
          <model-viewer
            src="{{ asset('storage/'.$model3d->file_path) }}"
            alt="{{ $artifact->name }}"
            auto-rotate auto-rotate-delay="500"
            camera-controls
            shadow-intensity="1.2"
            environment-image="neutral"
            exposure="0.85"
            tone-mapping="commerce"
            style="width:100%;height:100%;">
          </model-viewer>
          <div class="model-badge">Modèle 3D interactif</div>
          <div class="model-hint">
            <svg viewBox="0 0 24 24"><path d="M8 3l4 8 5-5 1 10-7-3-4 4-2-8 3-6z"/></svg>
            Faites pivoter avec votre doigt ou la souris
          </div>
        </div>
      </div>
      @endif


      {{-- ── HISTORICAL FIGURE ──────────────── --}}
      @if($figure && $figureArtifacts && $figureArtifacts->isNotEmpty())
      <div class="figure-section reveal reveal-delay-3">
        <div class="figure-header">
          <div class="figure-avatar">
            {{ mb_substr($figure->name, 0, 1) }}
          </div>
          <div>
            <div class="figure-name">{{ $figure->name }}</div>
            @if(isset($figure->name_fr))
            <div class="figure-name-fr">{{ $figure->name_fr }}
              @if($figure->birth_year) · {{ $figure->birth_year }}–{{ $figure->death_year ?? '?' }} @endif
            </div>
            @endif
          </div>
        </div>
        <div class="section-label" style="margin-bottom:14px">Objets associés à ce personnage</div>
        <div class="figure-related-grid">
          @foreach($figureArtifacts->take(6) as $rel)
          <a class="figure-related-item" href="{{ route('artifacts.show', $rel->qr_code) }}">
            @php $relImg = $rel->media->where('type','image')->where('is_main',true)->first() ?? $rel->media->where('type','image')->first(); @endphp
            @if($relImg)
            <img class="figure-related-img" src="{{ asset('storage/'.$relImg->file_path) }}" alt="{{ $rel->name }}"/>
            @else
            <div class="figure-related-no-img">
              <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
            </div>
            @endif
            <div class="figure-related-name">{{ $rel->name }}</div>
          </a>
          @endforeach
        </div>
      </div>
      @endif


      {{-- ── FEEDBACK SECTION ──────────────── --}}
      <div class="feedback-section reveal">
        <div class="section-label">Avis des visiteurs</div>

        @php
          $avgRating = $feedbacks->count() ? round($feedbacks->avg('rating'), 1) : null;
          $fullStars = $avgRating ? floor($avgRating) : 0;
        @endphp

        @if($feedbacks->count())
        <div class="feedback-stats">
          <div>
            <div class="feedback-avg">{{ number_format($avgRating, 1) }}</div>
            <div class="feedback-stars">
              @for($s=1;$s<=5;$s++)
                @if($s <= $fullStars) <span class="star-full">★</span>
                @else <span class="star-empty">★</span>
                @endif
              @endfor
            </div>
            <div class="feedback-count">{{ $feedbacks->total() }} avis</div>
          </div>
        </div>
        @endif

        {{-- Feedback cards --}}
        @foreach($feedbacks as $fb)
        <div class="fb-card">
          <div class="fb-head">
            <div class="fb-author">
              <div class="fb-avatar">
                {{ strtoupper(substr($fb->visitor_name ?: 'V', 0, 1)) }}
              </div>
              <div>
                <div class="fb-name">{{ $fb->visitor_name ?: 'Visiteur anonyme' }}</div>
                <div class="fb-date">{{ $fb->created_at->diffForHumans() }}</div>
              </div>
            </div>
            <div class="fb-stars">
              {{ str_repeat('★', $fb->rating) }}{{ str_repeat('☆', 5 - $fb->rating) }}
            </div>
          </div>
          @if($fb->comment)
          <p class="fb-comment">"{{ $fb->comment }}"</p>
          @endif
        </div>
        @endforeach

        @if($feedbacks->hasMorePages())
        <div style="text-align:center;margin-top:12px">{{ $feedbacks->links() }}</div>
        @endif

        {{-- Feedback form --}}
        <div class="fb-form">
          <p class="fb-form-title">Laisser un avis</p>

          @if(session('feedback_success'))
          <div style="background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);border-radius:6px;padding:11px 14px;margin-bottom:16px;color:#86efac;font-size:13.5px">
            ✓ Merci pour votre avis !
          </div>
          @endif

          <form method="POST" action="{{ route('artifacts.feedback', $artifact->qr_code) }}" novalidate>
            @csrf

            <div class="star-input" id="starInput">
              @for($r=5;$r>=1;$r--)
              <input type="radio" name="rating" id="star{{ $r }}" value="{{ $r }}"
                     {{ old('rating',5) == $r ? 'checked' : '' }}/>
              <label for="star{{ $r }}">★</label>
              @endfor
            </div>

            <input class="fb-input" type="text" name="visitor_name"
                   value="{{ old('visitor_name') }}"
                   placeholder="Votre nom (optionnel)"/>

            <textarea class="fb-textarea" name="comment"
                      placeholder="Partagez votre expérience avec cet artefact…">{{ old('comment') }}</textarea>

            @error('comment')<div style="color:#fca5a5;font-size:12px;margin-bottom:10px">{{ $message }}</div>@enderror

            <button type="submit" class="fb-submit">
              <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" fill="currentColor"/></svg>
              Publier l'avis
            </button>
          </form>
        </div>
      </div>

    </div>{{-- /left --}}


    {{-- RIGHT : Sidebar ─────────────────── --}}
    <div class="sidebar-sticky">

      {{-- Info card --}}
      <div class="info-card">
        <div class="info-card-head">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          Informations
        </div>
        <div class="info-card-body">
          <div class="info-row">
            <div class="info-label">Nom</div>
            <div class="info-val">{{ $artifact->name }}</div>
          </div>
          @if($artifact->year)
          <div class="info-row">
            <div class="info-label">Époque</div>
            <div class="info-val">{{ $artifact->year }}</div>
          </div>
          @endif
          @if($artifact->category)
          <div class="info-row">
            <div class="info-label">Catégorie</div>
            <div class="info-val">{{ $artifact->category->name }}</div>
          </div>
          @endif
          <div class="info-row">
            <div class="info-label">Localisation</div>
            <div class="info-val">Étage {{ $artifact->floor }}{{ $artifact->room ? ' · '.$artifact->room : '' }}</div>
          </div>
          @if($artifact->has_3d_model)
          <div class="info-row">
            <div class="info-label">Modèle 3D</div>
            <div><span class="badge-3d">Disponible</span></div>
          </div>
          @endif
          @if($audio)
          <div class="info-row">
            <div class="info-label">Narration</div>
            <div style="font-size:13px;color:#86efac">🎧 Audio disponible</div>
          </div>
          @endif
          <div class="info-row">
            <div class="info-label">Vues</div>
            <div style="font-family:var(--ff);font-size:22px;font-weight:300;color:var(--gold-lt);line-height:1">
              {{ $artifact->visits_count ?? $artifact->visits()->count() }}
            </div>
          </div>
        </div>
      </div>

      {{-- QR card --}}
      @if($artifact->qr_image_path)
      <div class="qr-card">
        <div class="qr-card-label">Code QR de cet artefact</div>
        <img class="qr-card-img"
             src="{{ asset('storage/'.$artifact->qr_image_path) }}"
             alt="QR {{ $artifact->name }}"/>
        <div class="qr-card-code">{{ $artifact->qr_code }}</div>
      </div>
      @endif

      {{-- Languages available --}}
      <div class="info-card">
        <div class="info-card-head">
          <svg viewBox="0 0 24 24"><path d="M5 8l6 6M4 14l6-6 2-3M2 5h12M7 2h1M22 22l-5-10-5 10M14 18h6"/></svg>
          Disponible en
        </div>
        <div class="info-card-body" style="padding:14px 18px">
          <div style="display:flex;flex-wrap:wrap;gap:7px">
            @foreach(['ar'=>'🇩🇿','fr'=>'🇫🇷','en'=>'🇬🇧','es'=>'🇪🇸','zh'=>'🇨🇳','ru'=>'🇷🇺'] as $loc => $flag)
            @if(isset($translations[$loc]) && $translations[$loc]->name)
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:999px;font-size:12px;background:rgba(200,168,75,.1);border:1px solid rgba(200,168,75,.2);color:var(--gold-lt)">
              {{ $flag }} {{ strtoupper($loc) }}
            </span>
            @endif
            @endforeach
          </div>
        </div>
      </div>

    </div>{{-- /sidebar --}}
  </div>{{-- /content-grid --}}

</div>{{-- /artifact-body --}}


{{-- ══════════════════════════════════════
     LIGHTBOX
══════════════════════════════════════ --}}
<div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Galerie d'images">
  <span class="lb-counter" id="lbCounter">1 / 1</span>
  <button class="lb-close" onclick="closeLightbox()" type="button" aria-label="Fermer">✕</button>
  <button class="lb-nav prev" id="lbPrev" onclick="lbMove(-1)" type="button" aria-label="Précédent">
    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <div class="lb-img-wrap">
    <img id="lbImg" class="lb-img" src="" alt=""/>
  </div>
  <button class="lb-nav next" id="lbNext" onclick="lbMove(1)" type="button" aria-label="Suivant">
    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  </button>
  <div class="lb-dots" id="lbDots"></div>
</div>


{{-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ --}}
<footer class="footer">
  <div class="footer-deco">
    <div class="fd-l l"></div>
    <div class="fd-d"></div>
    <div class="fd-l r"></div>
  </div>
  <div class="footer-name">المتحف المركزي للجيش</div>
  <div class="footer-copy">© {{ date('Y') }} · ANP — Direction de l'Information et de la Communication</div>
</footer>


{{-- ══════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════ --}}
<script>
/* ── Image data for lightbox ───────────────── */
var LB_IMAGES = [
  @foreach($images as $img)
  { src: '{{ asset('storage/'.$img->file_path) }}', alt: '{{ addslashes($artifact->name) }}' },
  @endforeach
];

/* ════════════════════════════════════════════
   HERO BG PARALLAX + LOAD ANIMATION
════════════════════════════════════════════ */
(function(){
  var bg = document.getElementById('heroBg');
  if (bg) {
    setTimeout(function(){ bg.classList.add('loaded'); }, 100);
    window.addEventListener('scroll', function(){
      var y = window.scrollY;
      bg.style.transform = 'scale(1) translateY(' + (y * .25) + 'px)';
    }, { passive:true });
  }
})();

/* ════════════════════════════════════════════
   LANGUAGE SWITCHER
════════════════════════════════════════════ */
var CURRENT_LANG = 'fr';

function toggleLang() {
  document.getElementById('langWrap').classList.toggle('open');
}

function switchLang(loc, btn) {
  CURRENT_LANG = loc;

  /* Close sheet */
  document.getElementById('langWrap').classList.remove('open');

  /* Update button label */
  document.getElementById('langCurrent').textContent = loc.toUpperCase();

  /* Active row */
  document.querySelectorAll('.lang-row').forEach(function(r){ r.classList.remove('active'); });
  btn.classList.add('active');

  /* Show/hide hero titles */
  ['fr','en','es','zh','ru','ar'].forEach(function(l){
    var el = document.getElementById('title'+l.charAt(0).toUpperCase()+l.slice(1));
    if (el) el.style.display = (l === loc) ? '' : 'none';
  });

  /* Show/hide descriptions */
  document.querySelectorAll('[id^="desc-"]').forEach(function(el){
    el.style.display = (el.getAttribute('data-locale') === loc) ? '' : 'none';
  });

  /* RTL for Arabic */
  document.body.dir = (loc === 'ar') ? 'rtl' : 'ltr';
}

/* Close lang dropdown on outside click */
document.addEventListener('click', function(e){
  var w = document.getElementById('langWrap');
  if (w && !w.contains(e.target)) w.classList.remove('open');
});

/* ════════════════════════════════════════════
   DESCRIPTION TOGGLE
════════════════════════════════════════════ */
function toggleDesc(loc) {
  var wrap = document.getElementById('descWrap-'+loc);
  var btn  = document.getElementById('toggle-'+loc);
  if (!wrap) return;
  var collapsed = wrap.classList.toggle('collapsed');
  btn.innerHTML = collapsed
    ? '<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg> Lire la suite'
    : '<svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg> Réduire';
  btn.classList.toggle('open', !collapsed);
}

/* ════════════════════════════════════════════
   AUDIO PLAYER
════════════════════════════════════════════ */
var audioEl   = document.getElementById('audioEl');
var audioBtn  = document.getElementById('audioPlayBtn');
var audioBar  = document.getElementById('audioBar');
var audioCur  = document.getElementById('audioCurrentTime');
var audioDur  = document.getElementById('audioDuration');
var audioWave = document.getElementById('audioWave');

function fmtTime(s){
  var m = Math.floor(s/60);
  var sec = Math.floor(s%60);
  return m+':'+(sec<10?'0':'')+sec;
}

if (audioEl) {
  audioEl.addEventListener('loadedmetadata', function(){
    audioDur.textContent = fmtTime(audioEl.duration);
  });
  audioEl.addEventListener('timeupdate', function(){
    var pct = audioEl.duration ? (audioEl.currentTime/audioEl.duration)*100 : 0;
    audioBar.style.width = pct+'%';
    audioCur.textContent = fmtTime(audioEl.currentTime);
  });
  audioEl.addEventListener('ended', function(){
    audioBtn.classList.remove('playing');
    if (audioWave) audioWave.classList.remove('playing');
    if (audioWave) audioWave.classList.add('paused');
  });
}

function toggleAudio(){
  if (!audioEl) return;
  if (audioEl.paused) {
    audioEl.play();
    audioBtn.classList.add('playing');
    if (audioWave) { audioWave.classList.add('playing'); audioWave.classList.remove('paused'); }
  } else {
    audioEl.pause();
    audioBtn.classList.remove('playing');
    if (audioWave) { audioWave.classList.remove('playing'); audioWave.classList.add('paused'); }
  }
}

function seekAudio(e){
  if (!audioEl || !audioEl.duration) return;
  var rect = e.currentTarget.getBoundingClientRect();
  var pct  = (e.clientX - rect.left) / rect.width;
  audioEl.currentTime = pct * audioEl.duration;
}

/* ════════════════════════════════════════════
   LIGHTBOX
════════════════════════════════════════════ */
var LB_INDEX = 0;

function buildDots(){
  var c = document.getElementById('lbDots');
  if (!c) return;
  c.innerHTML = '';
  LB_IMAGES.forEach(function(_,i){
    var d = document.createElement('div');
    d.className = 'lb-dot' + (i===LB_INDEX?' active':'');
    d.onclick = function(){ goToLb(i); };
    c.appendChild(d);
  });
}

function goToLb(i){
  LB_INDEX = i;
  var img = document.getElementById('lbImg');
  var counter = document.getElementById('lbCounter');
  var prev = document.getElementById('lbPrev');
  var next = document.getElementById('lbNext');
  if (!img) return;

  img.style.opacity='0';
  setTimeout(function(){
    img.src = LB_IMAGES[i].src;
    img.alt = LB_IMAGES[i].alt;
    img.style.opacity='1';
  },150);

  counter.textContent = (i+1)+' / '+LB_IMAGES.length;
  if (prev) prev.classList.toggle('disabled', i===0);
  if (next) next.classList.toggle('disabled', i===LB_IMAGES.length-1);

  document.querySelectorAll('.lb-dot').forEach(function(d,j){ d.classList.toggle('active',j===i); });
}

function openLightbox(i){
  if (!LB_IMAGES.length) return;
  LB_INDEX = i;
  document.getElementById('lightbox').classList.add('open');
  document.body.style.overflow='hidden';
  document.getElementById('lbImg').style.transition='opacity .15s';
  buildDots();
  goToLb(i);
}

function closeLightbox(){
  document.getElementById('lightbox').classList.remove('open');
  document.body.style.overflow='';
}

function lbMove(dir){
  var next = LB_INDEX + dir;
  if (next >= 0 && next < LB_IMAGES.length) goToLb(next);
}

/* Keyboard navigation */
document.addEventListener('keydown', function(e){
  var lb = document.getElementById('lightbox');
  if (!lb.classList.contains('open')) return;
  if (e.key==='ArrowRight') lbMove(1);
  if (e.key==='ArrowLeft')  lbMove(-1);
  if (e.key==='Escape')     closeLightbox();
});

/* Touch swipe */
(function(){
  var lb = document.getElementById('lightbox');
  if (!lb) return;
  var startX = 0;
  lb.addEventListener('touchstart',function(e){ startX=e.touches[0].clientX; },{passive:true});
  lb.addEventListener('touchend',function(e){
    var dx = e.changedTouches[0].clientX - startX;
    if (Math.abs(dx) > 50) lbMove(dx < 0 ? 1 : -1);
  });
})();

/* ════════════════════════════════════════════
   INTERSECTION OBSERVER — REVEAL
════════════════════════════════════════════ */
(function(){
  var els = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window)) {
    els.forEach(function(el){ el.classList.add('visible'); });
    return;
  }
  var obs = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        obs.unobserve(e.target);
      }
    });
  },{ threshold:.1, rootMargin:'0px 0px -40px 0px' });
  els.forEach(function(el){ obs.observe(el); });
})();

/* ════════════════════════════════════════════
   LOG VISIT (silent fetch)
════════════════════════════════════════════ */
(function(){
  var u = '{{ route('artifacts.visit', $artifact->qr_code) }}';
  if (!u) return;
  fetch(u,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}})
    .catch(function(){});
})();
</script>

</body>
</html>
