@extends('admin.layout')

@section('title', $isEdit ? 'Modifier un artefact' : 'Nouvel artefact')

@push('styles')
<style>
    .form-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }
    .form-head h1 {
        font-family: 'Cinzel', serif;
        font-size: clamp(22px, 3vw, 28px);
        color: var(--gold);
    }
    .form-head p { color: var(--muted); margin-top: 6px; max-width: 520px; font-size: 15px; }
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-ghost {
        background: rgba(200,168,75,0.08);
        color: var(--gold);
        border: 1px solid rgba(200,168,75,0.35);
    }
    .btn-ghost:hover { background: rgba(200,168,75,0.14); }
    .btn-submit {
        margin-top: 20px;
        background: linear-gradient(135deg, #c8a84b, #a88b3d);
        color: #1c1406;
        padding: 12px 28px;
        font-family: 'Cinzel', serif;
        font-size: 11px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .btn-submit:hover { filter: brightness(1.05); }
    .card {
        border: 1px solid rgba(200,168,75,0.35);
        border-radius: 12px;
        padding: 22px;
        background: var(--panel);
        box-shadow: 0 16px 40px rgba(0,0,0,0.25);
    }
    .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    label {
        display: block;
        font-family: 'Cinzel', serif;
        font-size: 10px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(200,168,75,0.55);
        margin-bottom: 8px;
    }
    input, select, textarea {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid rgba(200,168,75,0.28);
        border-radius: 8px;
        background: rgba(0,0,0,0.22);
        color: var(--text);
        font-size: 15px;
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: rgba(200,168,75,0.55);
    }
    textarea { min-height: 96px; resize: vertical; }
    .row { margin-bottom: 0; }
    .error { color: #fecaca; font-size: 13px; margin-top: 6px; }
    .locale {
        border: 1px solid rgba(200,168,75,0.15);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        background: rgba(0,0,0,0.15);
    }
    .locale strong {
        font-family: 'Cinzel', serif;
        font-size: 12px;
        color: var(--gold);
        letter-spacing: 0.1em;
    }
    .qr-box {
        border: 1px dashed rgba(200,168,75,0.35);
        border-radius: 10px;
        padding: 14px;
        background: rgba(0,0,0,0.18);
    }
    .qr-preview {
        width: 160px;
        height: 160px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid rgba(200,168,75,0.3);
        background: #fff;
        padding: 8px;
    }
    .qr-box a { color: var(--gold); font-size: 14px; }
    h3.section-title {
        font-family: 'Cinzel', serif;
        font-size: 14px;
        color: rgba(200,168,75,0.75);
        margin: 26px 0 14px;
        letter-spacing: 0.08em;
    }
    .check-row label { text-transform: none; letter-spacing: 0; font-family: inherit; font-size: 14px; color: var(--muted); }
    .check-row input { width: auto; margin-right: 8px; }
</style>
@endpush

@section('content')
    <div class="form-head">
        <div>
            <h1>{{ $isEdit ? 'Modifier l’artefact' : 'Nouvel artefact' }}</h1>
            <p>L’image QR est générée automatiquement à partir de l’identifiant QR. Complétez les traductions pour le public.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.artifacts.index') }}">← Liste</a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.artifacts.update', $artifact) : route('admin.artifacts.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card">
            <div class="grid">
                <div class="row">
                    <label>Nom</label>
                    <input name="name" value="{{ old('name', $artifact->name) }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <label>Code QR (identifiant)</label>
                    <input name="qr_code" value="{{ old('qr_code', $artifact->qr_code) }}" required>
                    @error('qr_code') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <label>Image QR générée</label>
                    <div class="qr-box">
                        @if($isEdit && $artifact->qr_image_path)
                            <img class="qr-preview" src="{{ asset('storage/' . $artifact->qr_image_path) }}" alt="QR">
                            <div style="margin-top:10px"><a href="{{ asset('storage/' . $artifact->qr_image_path) }}" target="_blank" rel="noopener">Ouvrir en grand</a></div>
                        @else
                            <span style="color:var(--muted);font-size:14px;">L’image sera créée à l’enregistrement.</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <label>Catégorie</label>
                    <select name="category_id" required>
                        <option value="">—</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $artifact->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <label>Étage</label>
                    <input type="number" name="floor" min="1" max="9" value="{{ old('floor', $artifact->floor ?: 1) }}" required>
                    @error('floor') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <label>URL modèle 3D (GLB)</label>
                    <input name="model_3d_path" value="{{ old('model_3d_path', optional($artifact->media->where('type', 'model_3d')->where('is_main', true)->first())->file_path) }}">
                </div>
                <div class="row check-row" style="display:flex;align-items:center;padding-top:22px;">
                    <label style="display:flex;align-items:center;cursor:pointer;">
                        <input type="checkbox" name="has_3d_model" value="1" @checked(old('has_3d_model', $artifact->has_3d_model))>
                        Modèle 3D disponible
                    </label>
                </div>
            </div>

            <h3 class="section-title">Traductions</h3>
            @foreach(['ar', 'es', 'fr', 'en', 'zh'] as $locale)
                <div class="locale">
                    <strong>{{ strtoupper($locale) }}</strong>
                    <div class="grid" style="margin-top:12px;">
                        <div class="row">
                            <label>Titre</label>
                            <input name="translations[{{ $locale }}][name]"
                                   value="{{ old("translations.$locale.name", optional($translations[$locale] ?? null)->name) }}">
                        </div>
                        <div class="row">
                            <label>Description</label>
                            <textarea name="translations[{{ $locale }}][description]">{{ old("translations.$locale.description", optional($translations[$locale] ?? null)->description) }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            <button class="btn btn-submit" type="submit">{{ $isEdit ? 'Enregistrer' : 'Créer' }}</button>
        </div>
    </form>
@endsection
