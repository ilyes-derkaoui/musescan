@extends('admin.layout')

@section('title', 'Artefacts')

@push('styles')
<style>
    .page-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-head h1 {
        font-family: 'Cinzel', serif;
        font-size: clamp(22px, 3vw, 28px);
        font-weight: 600;
        color: var(--gold);
    }
    .page-head p { color: var(--muted); margin-top: 6px; font-size: 15px; max-width: 520px; }
    .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .search-wrap {
        position: relative;
    }
    .search-wrap input {
        width: 220px;
        max-width: 100%;
        padding: 10px 14px 10px 36px;
        border: 1px solid var(--gold-dim);
        border-radius: 8px;
        background: rgba(0,0,0,0.25);
        color: var(--text);
        font-size: 14px;
    }
    .search-wrap input:focus { outline: none; border-color: rgba(200,168,75,0.55); }
    .search-wrap::before {
        content: '⌕';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.45;
        font-size: 16px;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform 0.12s, box-shadow 0.12s, background 0.15s;
    }
    .btn-primary {
        background: linear-gradient(135deg, #c8a84b, #a88b3d);
        color: #1c1406;
        box-shadow: 0 4px 14px rgba(0,0,0,0.35);
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,168,75,0.2); }
    .btn-ghost {
        background: rgba(200,168,75,0.08);
        color: var(--gold);
        border: 1px solid var(--gold-dim);
    }
    .btn-ghost:hover { background: rgba(200,168,75,0.14); }
    .btn-danger {
        background: rgba(220,38,38,0.2);
        color: #fecaca;
        border: 1px solid rgba(220,38,38,0.45);
    }
    .btn-danger:hover { background: rgba(220,38,38,0.35); }
    .btn-sm { padding: 8px 12px; font-size: 13px; }
    .flash {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 18px;
        background: rgba(34,197,94,0.12);
        border: 1px solid rgba(34,197,94,0.35);
        color: #bbf7d0;
        font-size: 14px;
    }
    .table-card {
        border: 1px solid var(--gold-dim);
        border-radius: 12px;
        overflow: hidden;
        background: var(--panel);
        box-shadow: 0 16px 40px rgba(0,0,0,0.25);
    }
    .table-scroll { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }
    th {
        text-align: left;
        padding: 14px 16px;
        font-family: 'Cinzel', serif;
        font-size: 10px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(200,168,75,0.55);
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid var(--gold-dim);
    }
    td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(200,168,75,0.1);
        font-size: 14px;
        vertical-align: middle;
    }
    tbody tr {
        transition: background 0.15s;
    }
    tbody tr:hover { background: rgba(200,168,75,0.06); }
    tbody tr.hidden { display: none; }
    .chip {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(200,168,75,0.12);
        color: var(--gold);
    }
    .qr-preview {
        width: 52px;
        height: 52px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid var(--gold-dim);
        padding: 4px;
        background: #fff;
    }
    .actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .mono { font-family: ui-monospace, monospace; font-size: 13px; color: rgba(232,228,217,0.85); }
    .pagination-wrap { margin-top: 20px; }
    .pagination-wrap nav { display: flex; justify-content: center; }
    .pagination-wrap a, .pagination-wrap span {
        display: inline-block;
        margin: 0 4px;
        padding: 8px 12px;
        border-radius: 6px;
        color: var(--muted);
        text-decoration: none;
        border: 1px solid transparent;
    }
    .pagination-wrap a:hover { border-color: var(--gold-dim); color: var(--text); }
    .pagination-wrap span[aria-current="page"] {
        background: rgba(200,168,75,0.2);
        color: var(--gold);
        border-color: var(--gold-dim);
    }
</style>
@endpush

@section('content')
    <div class="page-head">
        <div>
            <h1>Artefacts</h1>
            <p>Gérez les fiches, les traductions et les images QR à imprimer pour le musée.</p>
        </div>
        <div class="toolbar">
            <div class="search-wrap">
                <input type="search" id="artifactSearch" placeholder="Rechercher…" autocomplete="off" aria-label="Filtrer la liste">
            </div>
            <a class="btn btn-primary" href="{{ route('admin.artifacts.create') }}">+ Nouvel artefact</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>QR</th>
                        <th>Image QR</th>
                        <th>Catégorie</th>
                        <th>Étage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="artifactTableBody">
                    @forelse($artifacts as $artifact)
                        <tr data-search="{{ strtolower($artifact->name.' '.$artifact->qr_code.' '.($artifact->category?->name ?? '')) }}">
                            <td>{{ $artifact->id }}</td>
                            <td>{{ $artifact->name }}</td>
                            <td class="mono">{{ $artifact->qr_code }}</td>
                            <td>
                                @if($artifact->qr_image_path)
                                    <a href="{{ asset('storage/' . $artifact->qr_image_path) }}" target="_blank" rel="noopener">
                                        <img class="qr-preview" src="{{ asset('storage/' . $artifact->qr_image_path) }}" alt="">
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="chip">{{ $artifact->category?->name }}</span></td>
                            <td>{{ $artifact->floor }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.artifacts.edit', $artifact) }}">Modifier</a>
                                    <form method="POST" action="{{ route('admin.artifacts.destroy', $artifact) }}" onsubmit="return confirm('Supprimer cet artefact ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">Aucun artefact.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrap">
        {{ $artifacts->links() }}
    </div>
@endsection

@push('scripts')
<script>
(function() {
    var input = document.getElementById('artifactSearch');
    var body = document.getElementById('artifactTableBody');
    if (!input || !body) return;
    input.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        body.querySelectorAll('tr[data-search]').forEach(function(row) {
            var hay = row.getAttribute('data-search') || '';
            row.classList.toggle('hidden', q !== '' && hay.indexOf(q) === -1);
        });
    });
})();
</script>
@endpush
