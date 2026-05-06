{{-- Liste des avis visiteurs — toutes fiches --}}
@extends('admin.layout')

@section('title','Avis visiteurs')

@push('styles')
<style>
.fb-table .star-cell{color:var(--gold);letter-spacing:.06em;white-space:nowrap}
.fb-table .msg-cell{max-width:360px;font-size:13.2px;color:var(--muted);line-height:1.5}
.fb-table .msg-cell p{margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
</style>
@endpush

@section('content')

<div class="page-head">
  <div class="page-head-left">
    <h1>Avis visiteurs</h1>
    <p>Consultez les retours laissés depuis les fiches publiques exposées via QR code.</p>
  </div>
  <div class="page-head-right">
    <a class="btn btn-ghost" href="{{ route('admin.artifacts.index') }}">
      <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
      Artefacts
    </a>
    <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">
      <svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 9l-6 6-3-3-4 4"/></svg>
      Statistiques
    </a>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="fb-table">
      <thead>
        <tr>
          <th style="width:44px">#</th>
          <th>Artefact</th>
          <th style="width:100px;text-align:center">Note</th>
          <th>Message</th>
          <th style="width:140px">Visiteur</th>
          <th style="width:120px">Date</th>
          <th style="width:110px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($feedbacks as $fb)
        <tr>
          <td style="color:var(--muted2);font-size:12px">{{ $fb->id }}</td>
          <td>
            @if($fb->artifact)
              <a href="{{ route('admin.artifacts.edit', $fb->artifact) }}" style="font-weight:500;color:var(--text);text-decoration:none">
                {{ Str::limit($fb->artifact->name, 48) }}
              </a>
              <div style="font-size:11px;color:var(--muted2);margin-top:2px">{{ $fb->artifact->category?->name ?? '—' }}</div>
            @else
              <span style="color:var(--muted2)">—</span>
            @endif
          </td>
          <td style="text-align:center" class="star-cell">
            {{ str_repeat('★', (int) $fb->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $fb->rating)) }}
          </td>
          <td class="msg-cell">
            @if($fb->comment)<p>{{ $fb->comment }}</p>@else<span style="color:var(--muted2)">—</span>@endif
          </td>
          <td style="font-size:13px">{{ $fb->visitor_name ?: '—' }}</td>
          <td style="font-size:12px;color:var(--muted2)">{{ $fb->created_at->format('d/m/Y H:i') }}</td>
          <td>
            @if($fb->artifact)
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.artifacts.edit', $fb->artifact) }}">Fiche</a>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="empty-state" style="padding:48px 24px;text-align:center">
            <h3>Aucun avis</h3>
            <p style="margin-top:8px;color:var(--muted2)">Les visiteurs pourront déposer leur avis après un scan depuis la présentation publique.</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($feedbacks->hasPages())
<div class="pagination" style="margin-top:22px">{{ $feedbacks->links() }}</div>
@endif

@endsection
