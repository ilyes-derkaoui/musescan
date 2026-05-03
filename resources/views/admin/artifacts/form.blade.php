{{-- resources/views/admin/artifacts/form.blade.php --}}
@extends('admin.layout')

@section('title', $isEdit ? 'Modifier — '.$artifact->name : 'Nouvel artefact')

@push('styles')
<style>
/* ── Form layout ────────────────────────── */
.form-layout{
  display:grid;
  grid-template-columns:1fr 300px;
  gap:20px;
  align-items:start;
}
@media(max-width:960px){.form-layout{grid-template-columns:1fr}}

/* ── Sidebar panel (QR + meta) ──────────── */
.form-aside{
  display:flex;flex-direction:column;gap:16px;
  position:sticky;top:calc(var(--topbar-h,0px) + 20px);
}
@media(max-width:960px){.form-aside{position:static}}

.aside-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  padding:18px;
}
.aside-card-title{
  font-family:var(--fc);
  font-size:9.5px;letter-spacing:.18em;text-transform:uppercase;
  color:rgba(200,168,75,.4);
  margin-bottom:14px;
  display:flex;align-items:center;gap:8px;
}
.aside-card-title::after{content:'';flex:1;height:1px;background:var(--border)}

/* ── Progress bar (steps) ───────────────── */
.form-steps{
  display:flex;gap:0;margin-bottom:24px;
  background:rgba(0,0,0,.2);
  border:1px solid var(--border);
  border-radius:8px;
  overflow:hidden;
}
.form-step{
  flex:1;display:flex;align-items:center;gap:8px;
  padding:10px 14px;
  font-size:12px;font-weight:500;
  color:var(--muted);
  border-right:1px solid var(--border);
  cursor:pointer;
  transition:background .18s,color .18s;
  white-space:nowrap;
}
.form-step:last-child{border-right:none}
.form-step.active{background:rgba(200,168,75,.1);color:var(--gold-lt)}
.form-step.done{color:rgba(74,222,128,.7)}
.form-step-num{
  width:20px;height:20px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:10px;font-weight:700;font-family:var(--fc);
  background:rgba(255,255,255,.06);border:1px solid var(--border);
  flex-shrink:0;transition:all .18s;
}
.form-step.active .form-step-num{background:var(--gold);border-color:var(--gold);color:var(--bg)}
.form-step.done .form-step-num{background:rgba(74,222,128,.2);border-color:rgba(74,222,128,.4);color:#4ade80}
@media(max-width:600px){
  .form-step span:not(.form-step-num){display:none}
  .form-step{justify-content:center;padding:10px}
}

/* ── Main form card ─────────────────────── */
.form-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  overflow:hidden;
}
.form-card-body{padding:24px}

/* ── Translation tab system ─────────────── */
.locale-tabs{
  display:flex;flex-wrap:wrap;gap:6px;
  margin-bottom:20px;
}
.locale-tab{
  display:flex;align-items:center;gap:6px;
  padding:7px 14px;border-radius:6px;
  font-family:var(--fc);font-size:9.5px;
  letter-spacing:.16em;text-transform:uppercase;
  border:1px solid var(--border);background:transparent;
  color:var(--muted);cursor:pointer;
  transition:all .18s;
}
.locale-tab .flag{font-size:14px;line-height:1}
.locale-tab:hover{border-color:rgba(200,168,75,.28);color:var(--text)}
.locale-tab.active{
  background:rgba(200,168,75,.12);
  border-color:rgba(200,168,75,.32);
  color:var(--gold-lt);
}
.locale-tab .filled-dot{
  width:5px;height:5px;border-radius:50%;
  background:rgba(74,222,128,.6);
  margin-left:2px;display:none;
}
.locale-tab.has-content .filled-dot{display:block}

.locale-pane{display:none}
.locale-pane.active{display:grid;gap:16px}

/* ── Char counter ───────────────────────── */
.field-counter{
  display:flex;justify-content:space-between;
  align-items:center;gap:8px;
  margin-top:5px;
}
.char-count{font-size:11px;color:var(--muted2);font-family:'Courier New',monospace}
.char-count.warn{color:#fbbf24}
.char-count.over{color:#f87171}

/* ── Image upload drop zone ─────────────── */
.upload-zone{
  border:1px dashed rgba(200,168,75,.22);
  border-radius:8px;
  padding:24px 16px;
  text-align:center;
  cursor:pointer;
  transition:border-color .2s,background .2s;
  position:relative;
  background:rgba(200,168,75,.02);
}
.upload-zone:hover,.upload-zone.drag-over{
  border-color:rgba(200,168,75,.45);
  background:rgba(200,168,75,.05);
}
.upload-zone input[type=file]{
  position:absolute;inset:0;width:100%;height:100%;
  opacity:0;cursor:pointer;
}
.upload-zone-icon{
  width:40px;height:40px;
  border-radius:8px;
  background:rgba(200,168,75,.08);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 10px;
}
.upload-zone-icon svg{width:20px;height:20px;stroke:var(--gold);fill:none;stroke-width:1.5}
.upload-zone-text{font-size:13px;color:var(--muted);line-height:1.6}
.upload-zone-text strong{color:var(--gold-lt)}
.upload-zone-types{font-size:11px;color:var(--muted2);margin-top:4px}

.upload-preview{
  display:none;
  align-items:center;gap:14px;
  padding:12px 14px;
  background:rgba(0,0,0,.18);
  border:1px solid var(--border);
  border-radius:8px;
  margin-top:10px;
}
.upload-preview.visible{display:flex}
.upload-preview img{width:52px;height:52px;object-fit:cover;border-radius:6px;border:1px solid var(--border);flex-shrink:0}
.upload-preview-name{font-size:13px;color:var(--text);flex:1;min-width:0;word-break:break-all}
.upload-preview-rm{
  width:28px;height:28px;flex-shrink:0;
  background:rgba(176,42,42,.12);border:1px solid rgba(176,42,42,.25);
  border-radius:6px;color:#fca5a5;font-size:14px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:background .15s;
}
.upload-preview-rm:hover{background:rgba(176,42,42,.25)}

/* ── 3D model path field ────────────────── */
.model-field{
  background:rgba(124,58,237,.06);
  border:1px solid rgba(124,58,237,.2);
  border-radius:8px;
  padding:16px;
  transition:border-color .2s;
}
.model-field.hidden{display:none}
.model-field-label{
  display:flex;align-items:center;gap:8px;
  margin-bottom:10px;
  font-size:12px;color:#c4b5fd;font-weight:500;
}
.model-field-label svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:1.5}

/* ── Submit bar ─────────────────────────── */
.submit-bar{
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;flex-wrap:wrap;
  padding:18px 24px;
  border-top:1px solid var(--border);
  background:rgba(0,0,0,.12);
}
.submit-bar-left{font-size:12.5px;color:var(--muted)}

.btn-submit-form{
  display:inline-flex;align-items:center;gap:9px;
  padding:12px 28px;
  font-family:var(--fc);font-size:11px;font-weight:600;
  letter-spacing:.2em;text-transform:uppercase;
  color:var(--bg);
  background:linear-gradient(135deg,var(--gold-lt) 0%,var(--gold) 55%,var(--gold-dk) 100%);
  border:none;border-radius:6px;cursor:pointer;
  box-shadow:0 6px 20px rgba(0,0,0,.3),inset 0 1px 0 rgba(255,255,255,.12);
  transition:filter .2s,transform .15s;
  position:relative;overflow:hidden;
}
.btn-submit-form::before{
  content:'';
  position:absolute;top:0;left:-110%;width:60%;height:100%;
  background:linear-gradient(100deg,transparent,rgba(255,255,255,.22),transparent);
  transition:left .5s;
}
.btn-submit-form:hover::before{left:140%}
.btn-submit-form:hover{filter:brightness(1.06);transform:translateY(-1px)}
.btn-submit-form:active{transform:none}
.btn-submit-form svg{width:15px;height:15px;fill:currentColor;flex-shrink:0}
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────── --}}
<div class="page-head">
  <div class="page-head-left">
    <h1>{{ $isEdit ? 'Modifier l\'artefact' : 'Nouvel artefact' }}</h1>
    <p>{{ $isEdit ? 'Mettez à jour les informations et les traductions.' : 'Remplissez la fiche complète — le QR code sera généré automatiquement.' }}</p>
  </div>
  <div class="page-head-right">
    <a class="btn btn-ghost" href="{{ route('admin.artifacts.index') }}">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Retour à la liste
    </a>
  </div>
</div>

<form method="POST"
      action="{{ $isEdit ? route('admin.artifacts.update',$artifact) : route('admin.artifacts.store') }}"
      id="artifactForm" enctype="multipart/form-data" novalidate>
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="form-layout">

    {{-- ══ MAIN COLUMN ══ --}}
    <div>

      {{-- ── Section 1 : Informations générales ── --}}
      <div class="form-card" style="margin-bottom:16px">
        <div style="padding:18px 24px 0">
          <div class="section-title">
            <svg width="14" height="14" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Informations générales
          </div>
        </div>
        <div class="form-card-body" style="padding-top:0">
          <div class="form-grid">

            {{-- Nom --}}
            <div class="form-group">
              <label class="form-label" for="name">Nom de l'artefact <span style="color:#f87171">*</span></label>
              <input class="form-input {{ $errors->has('name') ? 'error-field' : '' }}"
                     id="name" name="name"
                     value="{{ old('name',$artifact->name) }}"
                     required placeholder="ex: Sabre ottoman 1750"/>
              @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Code QR --}}
            <div class="form-group">
              <label class="form-label" for="qr_code">Identifiant QR <span style="color:#f87171">*</span></label>
              <input class="form-input {{ $errors->has('qr_code') ? 'error-field' : '' }}"
                     id="qr_code" name="qr_code"
                     value="{{ old('qr_code',$artifact->qr_code) }}"
                     required placeholder="ex: ART-2024-001"/>
              <div class="form-hint">Identifiant unique, sans espaces. Servira d'URL pour le QR code.</div>
              @error('qr_code')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Catégorie --}}
            <div class="form-group">
              <label class="form-label" for="category_id">Catégorie <span style="color:#f87171">*</span></label>
              <select class="form-select" id="category_id" name="category_id" required>
                <option value="">— Sélectionner —</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}"
                    {{ old('category_id',$artifact->category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
              @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Étage --}}
            <div class="form-group">
              <label class="form-label" for="floor">Étage <span style="color:#f87171">*</span></label>
              <select class="form-select" id="floor" name="floor" required>
                @for($i=1;$i<=4;$i++)
                  <option value="{{ $i }}"
                    {{ old('floor',$artifact->floor ?? 1) == $i ? 'selected' : '' }}>
                    Étage {{ $i }}
                  </option>
                @endfor
              </select>
              @error('floor')<div class="form-error">{{ $message }}</div>@enderror
            </div>

          </div>{{-- /grid --}}

          {{-- 3D model option --}}
          <div style="margin-top:18px">
            <label class="check-wrap">
              <input type="checkbox" name="has_3d_model" value="1" id="has3d"
                     {{ old('has_3d_model',$artifact->has_3d_model) ? 'checked' : '' }}
                     onchange="toggle3dField(this.checked)">
              <div>
                <span class="badge-3d" style="margin-right:6px">3D</span>
                Modèle 3D disponible pour cet artefact
              </div>
            </label>
          </div>

          {{-- 3D path (hidden unless checked) --}}
          <div class="model-field {{ old('has_3d_model',$artifact->has_3d_model) ? '' : 'hidden' }}"
               id="model3dField" style="margin-top:14px">
            <div class="model-field-label">
              <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="7.5 4.21 12 6.81 16.5 4.21"/><polyline points="7.5 19.79 7.5 14.6 3 12"/><polyline points="21 12 16.5 14.6 16.5 19.79"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
              URL du fichier GLB/GLTF
            </div>
            <input class="form-input"
                   name="model_3d_path"
                   value="{{ old('model_3d_path', optional($artifact->media->where('type','model_3d')->where('is_main',true)->first())->file_path) }}"
                   placeholder="https://cdn.musee.dz/models/artifact.glb"/>
          </div>

        </div>
      </div>{{-- /card --}}

      {{-- ── Section 2 : Traductions ── --}}
      <div class="form-card">
        <div style="padding:18px 24px 0">
          <div class="section-title">
            <svg width="14" height="14" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0"><path d="M5 8l6 6M4 14l6-6 2-3M2 5h12M7 2h1M22 22l-5-10-5 10M14 18h6"/></svg>
            Traductions &amp; descriptions
          </div>
        </div>
        <div class="form-card-body" style="padding-top:8px">

          <div class="locale-tabs" id="localeTabs">
            @foreach([
              ['ar','🇩🇿','Arabe','ar'],
              ['fr','🇫🇷','Français','fr'],
              ['en','🇬🇧','Anglais','en'],
              ['es','🇪🇸','Espagnol','es'],
              ['zh','🇨🇳','Chinois','zh'],
              ['ru','🇷🇺','Russe','ru'],
            ] as [$loc,$flag,$label,$key])
            <button type="button" class="locale-tab {{ $loc === 'ar' ? 'active' : '' }} {{ (isset($translations[$loc]) && $translations[$loc]->name) ? 'has-content' : '' }}"
                    data-locale="{{ $loc }}" onclick="switchLocale('{{ $loc }}')">
              <span class="flag">{{ $flag }}</span>
              {{ $label }}
              <span class="filled-dot"></span>
            </button>
            @endforeach
          </div>

          @foreach([
            ['ar','🇩🇿','Arabe',true],
            ['fr','🇫🇷','Français',false],
            ['en','🇬🇧','Anglais',false],
            ['es','🇪🇸','Espagnol',false],
            ['zh','🇨🇳','Chinois',false],
            ['ru','🇷🇺','Russe',false],
          ] as [$loc,$flag,$label,$first])
          <div class="locale-pane {{ $first ? 'active' : '' }}" id="pane-{{ $loc }}">
            <div class="form-group" style="{{ $loc === 'ar' ? 'direction:rtl' : '' }}">
              <label class="form-label" for="title_{{ $loc }}">
                {{ $flag }} Titre en {{ $label }}
              </label>
              <input class="form-input"
                     id="title_{{ $loc }}"
                     name="translations[{{ $loc }}][name]"
                     value="{{ old("translations.$loc.name", optional($translations[$loc] ?? null)->name) }}"
                     placeholder="Titre de l'artefact"
                     oninput="onLocaleInput('{{ $loc }}',this)"/>
            </div>
            <div class="form-group" style="{{ $loc === 'ar' ? 'direction:rtl' : '' }}">
              <label class="form-label" for="desc_{{ $loc }}">Description en {{ $label }}</label>
              <textarea class="form-textarea"
                        id="desc_{{ $loc }}"
                        name="translations[{{ $loc }}][description]"
                        rows="5"
                        maxlength="2000"
                        placeholder="Description historique détaillée…"
                        oninput="countChars(this,'counter_{{ $loc }}')">{{ old("translations.$loc.description", optional($translations[$loc] ?? null)->description) }}</textarea>
              <div class="field-counter">
                <div class="form-hint" style="margin:0">Décrivez l'histoire, l'origine et la signification de l'objet.</div>
                <span class="char-count" id="counter_{{ $loc }}">0 / 2000</span>
              </div>
            </div>
          </div>
          @endforeach

        </div>

        {{-- Submit bar --}}
        <div class="submit-bar">
          <span class="submit-bar-left">
            @if($isEdit)
              Dernière modification : {{ $artifact->updated_at->diffForHumans() }}
            @else
              Le QR code sera généré automatiquement à l'enregistrement.
            @endif
          </span>
          <div style="display:flex;gap:10px;align-items:center">
            <a class="btn btn-ghost" href="{{ route('admin.artifacts.index') }}">Annuler</a>
            <button type="submit" class="btn-submit-form" id="formSubmitBtn">
              <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              {{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'artefact' }}
            </button>
          </div>
        </div>

      </div>{{-- /card --}}
    </div>{{-- /main column --}}

    {{-- ══ ASIDE COLUMN ══ --}}
    <div class="form-aside">

      {{-- QR Code preview --}}
      <div class="aside-card">
        <div class="aside-card-title">
          <svg width="13" height="13" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
          Image QR code
        </div>
        @if($isEdit && $artifact->qr_image_path)
          <div style="text-align:center">
            <img class="qr-preview-img"
                 src="{{ asset('storage/'.$artifact->qr_image_path) }}"
                 alt="QR {{ $artifact->name }}"
                 style="width:150px;height:150px;object-fit:contain;border-radius:8px;border:1px solid rgba(200,168,75,.2);padding:10px;background:#fff;margin:0 auto 12px;display:block"/>
            <a class="btn btn-ghost btn-sm"
               href="{{ asset('storage/'.$artifact->qr_image_path) }}"
               target="_blank" rel="noopener" download>
              <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Télécharger
            </a>
          </div>
        @else
          <div class="qr-preview-pending">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            <span>Généré à l'enregistrement</span>
          </div>
        @endif
      </div>

      {{-- Status card --}}
      <div class="aside-card">
        <div class="aside-card-title">
          <svg width="13" height="13" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          Informations
        </div>
        <div style="display:flex;flex-direction:column;gap:12px">
          @if($isEdit)
          <div>
            <div style="font-size:11px;color:var(--muted2);margin-bottom:2px">Créé le</div>
            <div style="font-size:13px;color:var(--text)">{{ $artifact->created_at->format('d/m/Y à H:i') }}</div>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted2);margin-bottom:2px">Scans totaux</div>
            <div style="font-family:var(--ff);font-size:22px;font-weight:300;color:var(--gold-lt)">
              {{ $artifact->visits_count ?? $artifact->visits()->count() }}
            </div>
          </div>
          @endif
          <div>
            <div style="font-size:11px;color:var(--muted2);margin-bottom:4px">Traductions remplies</div>
            <div style="display:flex;gap:4px;flex-wrap:wrap" id="translationStatus">
              @foreach(['ar','fr','en','es','zh','ru'] as $loc)
                <span id="status-{{ $loc }}"
                      style="font-size:11px;padding:2px 7px;border-radius:999px;
                             {{ (isset($translations[$loc]) && $translations[$loc]->name) ?
                                'background:rgba(74,222,128,.1);color:#86efac;border:1px solid rgba(74,222,128,.2)' :
                                'background:rgba(255,255,255,.04);color:var(--muted2);border:1px solid var(--border)' }}">
                  {{ strtoupper($loc) }}
                </span>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Feedback preview (if edit) --}}
      @if($isEdit && isset($artifact->feedbacks) && $artifact->feedbacks->count() > 0)
      <div class="aside-card">
        <div class="aside-card-title">
          <svg width="13" height="13" viewBox="0 0 24 24" style="stroke:var(--gold);fill:none;stroke-width:1.5;flex-shrink:0"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
          Feedbacks visiteurs
        </div>
        @foreach($artifact->feedbacks->take(3) as $fb)
        <div style="border-bottom:1px solid var(--border);padding:10px 0;font-size:13px">
          <div style="display:flex;justify-content:space-between;margin-bottom:4px">
            <span style="color:var(--text)">{{ $fb->visitor_name ?: 'Anonyme' }}</span>
            <span style="color:var(--gold);letter-spacing:.06em">
              {{ str_repeat('★',$fb->rating) }}{{ str_repeat('☆',5-$fb->rating) }}
            </span>
          </div>
          @if($fb->comment)
            <div style="color:var(--muted);line-height:1.5;font-style:italic">"{{ Str::limit($fb->comment,80) }}"</div>
          @endif
        </div>
        @endforeach
        @if($artifact->feedbacks->count() > 3)
          <div style="font-size:12px;color:var(--muted2);margin-top:8px">
            + {{ $artifact->feedbacks->count() - 3 }} autre(s) feedback(s)
          </div>
        @endif
      </div>
      @endif

    </div>{{-- /aside --}}
  </div>{{-- /form-layout --}}
</form>

@endsection

@push('scripts')
<script>
/* ── Locale tab switcher ─────────────────────── */
function switchLocale(loc) {
  document.querySelectorAll('.locale-tab').forEach(function(t){t.classList.remove('active')});
  document.querySelectorAll('.locale-pane').forEach(function(p){p.classList.remove('active')});
  document.querySelector('[data-locale="'+loc+'"]').classList.add('active');
  var pane = document.getElementById('pane-'+loc);
  if (pane) pane.classList.add('active');
  /* Init char counters */
  var ta = document.getElementById('desc_'+loc);
  if (ta) countChars(ta, 'counter_'+loc);
}

/* ── Char counter ───────────────────────────── */
function countChars(el, counterId) {
  var max = parseInt(el.getAttribute('maxlength') || 2000);
  var n   = el.value.length;
  var el2 = document.getElementById(counterId);
  if (!el2) return;
  el2.textContent = n + ' / ' + max;
  el2.className = 'char-count' + (n > max ? ' over' : n > max * .85 ? ' warn' : '');
}

/* ── Mark tab as filled ─────────────────────── */
function onLocaleInput(loc, input) {
  var tab = document.querySelector('[data-locale="'+loc+'"]');
  if (!tab) return;
  var filled = !!input.value.trim();
  tab.classList.toggle('has-content', filled);
  /* Update aside status badge */
  var badge = document.getElementById('status-'+loc);
  if (badge) {
    if (filled) {
      badge.style.cssText = 'font-size:11px;padding:2px 7px;border-radius:999px;background:rgba(74,222,128,.1);color:#86efac;border:1px solid rgba(74,222,128,.2)';
    } else {
      badge.style.cssText = 'font-size:11px;padding:2px 7px;border-radius:999px;background:rgba(255,255,255,.04);color:var(--muted2);border:1px solid var(--border)';
    }
  }
}

/* ── 3D field toggle ────────────────────────── */
function toggle3dField(checked) {
  var f = document.getElementById('model3dField');
  if (f) f.classList.toggle('hidden', !checked);
}

/* ── Submit loading ─────────────────────────── */
document.getElementById('artifactForm').addEventListener('submit', function() {
  var btn = document.getElementById('formSubmitBtn');
  btn.disabled = true;
  btn.style.opacity = '.65';
  btn.style.cursor  = 'not-allowed';
});

/* ── Init counters on load ──────────────────── */
['ar','fr','en','es','zh','ru'].forEach(function(loc) {
  var ta = document.getElementById('desc_'+loc);
  if (ta) countChars(ta, 'counter_'+loc);
});
</script>
@endpush
