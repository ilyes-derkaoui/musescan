{{-- resources/views/admin/artifacts/index.blade.php --}}
@extends('admin.layout')

@section('title','Artefacts')

@push('styles')
<style>
/* ── Toolbar ──────────────────── */
.toolbar{display:flex;flex-wrap:wrap;gap:10px;align-items:center}

/* ── Mobile card view ─────────── */
.artifact-cards{
  display:none;
  flex-direction:column;
  gap:12px;
}
.artifact-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:10px;
  padding:16px;
  display:flex;gap:14px;align-items:flex-start;
  transition:border-color .18s;
}
.artifact-card:hover{border-color:rgba(200,168,75,.25)}
.artifact-card-qr{
  width:56px;height:56px;flex-shrink:0;
  object-fit:contain;
  border-radius:6px;
  border:1px solid rgba(200,168,75,.18);
  padding:4px;background:#fff;
}
.artifact-card-body{flex:1;min-width:0}
.artifact-card-name{font-size:15px;font-weight:500;color:var(--text);margin-bottom:5px}
.artifact-card-meta{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px}
.artifact-card-meta span{font-size:12px;color:var(--muted)}
.artifact-card-actions{display:flex;flex-wrap:wrap;gap:6px}

/* ── Responsive: show cards on mobile ── */
@media(max-width:720px){
  .artifact-cards{display:flex}
  .card.table-card{display:none}
}

/* ── Count badge ─────────────── */
.count-badge{
  font-family:'Courier New',monospace;
  font-size:11px;font-weight:700;
  padding:3px 8px;border-radius:999px;
  background:rgba(200,168,75,.1);color:var(--gold);
  margin-left:6px;
}

/* ── Floor indicator ─────────── */
.floor-badge{
  display:inline-flex;align-items:center;justify-content:center;
  width:26px;height:26px;
  border-radius:6px;
  background:rgba(200,168,75,.08);
  border:1px solid rgba(200,168,75,.18);
  font-family:var(--fc);font-size:10px;
  color:var(--gold-lt);font-weight:600;
}

/* ── Skeleton loading ─────────── */
.skeleton{
  background:linear-gradient(90deg,rgba(200,168,75,.04) 25%,rgba(200,168,75,.08) 50%,rgba(200,168,75,.04) 75%);
  background-size:200% 100%;
  animation:shimmer 1.5s infinite;
  border-radius:4px;
}
@keyframes shimmer{
  from{background-position:200% 0}
  to{background-position:-200% 0}
}

@keyframes popIn{
  from{opacity:0;transform:scale(.94) translateY(12px)}
  to{opacity:1;transform:scale(1) translateY(0)}
}

/* ── Empty row ─────────────────── */
.td-empty{text-align:center;padding:60px 24px!important}

/* ── Filter bar ─────────────────── */
.filter-bar{
  display:flex;flex-wrap:wrap;gap:8px;
  margin-bottom:20px;
  align-items:center;
}
.filter-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:6px 12px;border-radius:999px;
  font-size:12px;font-weight:500;
  border:1px solid var(--border);
  background:transparent;color:var(--muted);
  cursor:pointer;transition:all .15s;white-space:nowrap;
}
.filter-btn:hover{border-color:rgba(200,168,75,.3);color:var(--text)}
.filter-btn.active{
  background:rgba(200,168,75,.12);
  border-color:rgba(200,168,75,.3);
  color:var(--gold-lt);
}
.filter-btn .dot{
  width:6px;height:6px;border-radius:50%;
  background:currentColor;flex-shrink:0;
}
.filter-clear{
  font-size:12px;color:var(--muted2);
  background:none;border:none;cursor:pointer;
  padding:4px 8px;
  transition:color .15s;
  display:none;
}
.filter-clear:hover{color:var(--text)}
.filter-clear.visible{display:inline-flex;align-items:center;gap:4px}

</style>
@endpush

@section('content')

{{-- ── Page header ─────────────────────────── --}}
<div class="page-head">
  <div class="page-head-left">
    <h1>Artefacts <span class="count-badge">{{ $artifacts->total() }}</span></h1>
    <p>Gérez les fiches, les traductions et les codes QR à imprimer sur les vitrines.</p>
  </div>
  <div class="page-head-right">
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">
      <svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 9l-6 6-3-3-4 4"/></svg>
      Statistiques
    </a>
    <a class="btn btn-ghost" href="{{ route('admin.feedbacks.index') }}">
      <svg viewBox="0 0 24 24"><path d="M12 17.75l-6.172 3.848 1.639-7.052L2 9.798l7.229-.596L12 2.75l2.771 6.452 7.229.596-5.467 4.948 1.639 7.052z"/></svg>
      Avis
    </a>
    <div class="search-wrap">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input class="search-input" id="searchInput" type="search"
             placeholder="Rechercher…" autocomplete="off" aria-label="Filtrer les artefacts"/>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.artifacts.create') }}">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
      Nouvel artefact
    </a>
  </div>
</div>

{{-- ── Category filter bar ─────────────────── --}}
<div class="filter-bar" id="filterBar">
  <button class="filter-btn active" data-filter="all" onclick="applyFilter(this,'all')">
    <span class="dot" style="background:var(--gold)"></span>Tous
  </button>
  @foreach($categories ?? [] as $cat)
  <button class="filter-btn" data-filter="{{ $cat->id }}" onclick="applyFilter(this,'{{ $cat->id }}')">
    <span class="dot"></span>{{ $cat->name }}
  </button>
  @endforeach
  <button class="filter-clear" id="filterClear" onclick="clearFilter()">
    <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2"><path d="M18 6L6 18M6 6l12 12"/></svg>
    Effacer
  </button>
</div>

{{-- ── Desktop table ───────────────────────── --}}
<div class="card table-card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:46px">#</th>
          <th>Nom</th>
          <th>Code QR</th>
          <th style="width:80px">Image QR</th>
          <th>Catégorie</th>
          <th style="width:80px;text-align:center">Étage</th>
          <th style="width:60px">3D</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($artifacts as $artifact)
        <tr
          data-search="{{ strtolower($artifact->name.' '.$artifact->qr_code.' '.($artifact->category?->name ?? '')) }}"
          data-cat="{{ $artifact->category_id }}">
          <td style="color:var(--muted2);font-size:12px">{{ $artifact->id }}</td>
          <td>
            <span style="font-weight:500">{{ $artifact->name }}</span>
            @if($artifact->has_3d_model)
              <span class="badge-3d" style="margin-left:6px;font-size:9px;padding:2px 7px">3D</span>
            @endif
          </td>
          <td class="mono">{{ $artifact->qr_code }}</td>
          <td>
            @if($artifact->qr_image_path)
              <a href="{{ asset('storage/'.$artifact->qr_image_path) }}" target="_blank" rel="noopener">
                <img class="qr-thumb" src="{{ asset('storage/'.$artifact->qr_image_path) }}" alt="QR {{ $artifact->name }}"/>
              </a>
            @else
              <span style="color:var(--muted2);font-size:12px">—</span>
            @endif
          </td>
          <td>
            @if($artifact->category)
              <span class="chip">{{ $artifact->category->name }}</span>
            @else
              <span style="color:var(--muted2)">—</span>
            @endif
          </td>
          <td style="text-align:center">
            <span class="floor-badge">{{ $artifact->floor }}</span>
          </td>
          <td>
            @if($artifact->has_3d_model)
              <span style="color:#c4b5fd;font-size:18px">✦</span>
            @else
              <span style="color:var(--muted2);font-size:13px">—</span>
            @endif
          </td>
          <td>
            <div class="actions">
              <a class="btn btn-ghost btn-sm"
                 href="{{ route('admin.artifacts.edit', $artifact) }}"
                 title="Modifier">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Modifier
              </a>
              <form method="POST" action="{{ route('admin.artifacts.destroy', $artifact) }}"
                    onsubmit="return confirmDelete(event,'{{ addslashes($artifact->name) }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit" title="Supprimer">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  Suppr.
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="td-empty">
            <div class="empty-state">
              <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
              <h3>Aucun artefact</h3>
              <p>Commencez par créer le premier artefact du musée.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ── Mobile card view ────────────────────── --}}
<div class="artifact-cards" id="mobileCards">
  @forelse($artifacts as $artifact)
  <div class="artifact-card"
       data-search="{{ strtolower($artifact->name.' '.$artifact->qr_code.' '.($artifact->category?->name ?? '')) }}"
       data-cat="{{ $artifact->category_id }}">
    @if($artifact->qr_image_path)
      <img class="artifact-card-qr" src="{{ asset('storage/'.$artifact->qr_image_path) }}" alt="QR"/>
    @else
      <div class="artifact-card-qr" style="display:flex;align-items:center;justify-content:center;background:rgba(200,168,75,.06)">
        <svg width="24" height="24" viewBox="0 0 24 24" style="stroke:rgba(200,168,75,.25);fill:none;stroke-width:1">
          <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
        </svg>
      </div>
    @endif
    <div class="artifact-card-body">
      <div class="artifact-card-name">{{ $artifact->name }}</div>
      <div class="artifact-card-meta">
        @if($artifact->category)
          <span class="chip" style="font-size:11px;padding:2px 8px">{{ $artifact->category->name }}</span>
        @endif
        <span>Étage {{ $artifact->floor }}</span>
        @if($artifact->has_3d_model)
          <span class="badge-3d" style="font-size:9px;padding:2px 7px">3D</span>
        @endif
      </div>
      <div class="artifact-card-actions">
        <a class="btn btn-ghost btn-sm" href="{{ route('admin.artifacts.edit',$artifact) }}">Modifier</a>
        <form method="POST" action="{{ route('admin.artifacts.destroy',$artifact) }}"
              onsubmit="return confirmDelete(event,'{{ addslashes($artifact->name) }}')">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="empty-state">
    <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
    <h3>Aucun artefact</h3>
    <p>Commencez par créer le premier artefact.</p>
  </div>
  @endforelse
</div>

{{-- Pagination --}}
<div class="pagination" style="margin-top:22px">
  {{ $artifacts->links() }}
</div>

{{-- Delete confirm modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:250;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,.7);backdrop-filter:blur(6px)">
  <div style="background:var(--panel);border:1px solid rgba(200,168,75,.2);border-radius:10px;max-width:380px;width:100%;padding:28px;animation:popIn .25s ease both">
    <div style="width:44px;height:44px;border-radius:10px;background:rgba(176,42,42,.12);border:1px solid rgba(176,42,42,.25);display:flex;align-items:center;justify-content:center;margin-bottom:16px">
      <svg width="20" height="20" viewBox="0 0 24 24" style="stroke:#fca5a5;fill:none;stroke-width:1.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
    </div>
    <h3 style="font-family:var(--ff);font-size:20px;font-weight:300;font-style:italic;color:var(--ivory);margin-bottom:8px">Supprimer cet artefact ?</h3>
    <p style="font-size:13.5px;color:var(--muted);margin-bottom:22px;line-height:1.6" id="deleteModalName">Cette action est irréversible.</p>
    <div style="display:flex;gap:10px;justify-content:flex-end">
      <button class="btn btn-ghost btn-sm" onclick="closeDeleteModal()" type="button">Annuler</button>
      <button class="btn btn-danger btn-sm" id="deleteConfirmBtn" type="button">Supprimer définitivement</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Search + filter ────────────────────────── */
var FILTER_CAT = 'all';
var SEARCH_Q   = '';

function applyFilter(btn, cat) {
  FILTER_CAT = cat;
  document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  var cl = document.getElementById('filterClear');
  if (cat !== 'all') cl.classList.add('visible'); else cl.classList.remove('visible');
  filterRows();
}

function clearFilter() {
  FILTER_CAT = 'all';
  document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
  document.querySelector('[data-filter="all"]').classList.add('active');
  document.getElementById('filterClear').classList.remove('visible');
  filterRows();
}

document.getElementById('searchInput').addEventListener('input', function() {
  SEARCH_Q = this.value.trim().toLowerCase();
  filterRows();
});

function filterRows() {
  var fc = FILTER_CAT === 'all' ? 'all' : String(FILTER_CAT);
  var allRows = document.querySelectorAll('#tableBody tr[data-search], #mobileCards [data-search]');
  var shown = 0;
  allRows.forEach(function(row) {
    var hay = (row.getAttribute('data-search') || '').toLowerCase();
    var cat = row.getAttribute('data-cat');
    cat = cat === null || cat === '' ? '' : String(cat);
    var matchQ   = !SEARCH_Q || hay.indexOf(SEARCH_Q) !== -1;
    var matchCat = fc === 'all' || cat === fc;
    var vis = matchQ && matchCat;
    row.style.display = vis ? '' : 'none';
    if (vis) shown++;
  });
}

/* ── Delete confirmation modal ──────────────── */
var pendingForm = null;

function confirmDelete(e, name) {
  e.preventDefault();
  pendingForm = e.target.closest('form');
  document.getElementById('deleteModalName').textContent =
    'L\'artefact "' + name + '" sera supprimé de façon permanente.';
  var m = document.getElementById('deleteModal');
  m.style.display = 'flex';
  document.getElementById('deleteConfirmBtn').focus();
  return false;
}

document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
  if (pendingForm) { pendingForm.submit(); }
});

function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
  pendingForm = null;
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endpush
