{{-- Tableau de bord — statistiques & graphiques --}}
@extends('admin.layout')

@section('title', 'Statistiques')

@push('styles')
<style>
.dash-chart-grid{
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(0,1.35fr);
  gap:18px;
  margin-bottom:22px;
}
@media(max-width:1024px){.dash-chart-grid{grid-template-columns:1fr}}
.chart-card{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 0 0 1px rgba(0,0,0,.2) inset;
}
.chart-card-head{
  padding:16px 20px 12px;
  border-bottom:1px solid var(--border);
  display:flex;
  flex-direction:column;
  gap:4px;
}
.chart-card-title{
  font-family:var(--fc);
  font-size:9.5px;
  letter-spacing:.2em;
  text-transform:uppercase;
  color:rgba(200,168,75,.52);
}
.chart-card-sub{
  font-family:var(--ff);
  font-size:clamp(17px,2vw,21px);
  font-weight:300;
  font-style:italic;
  color:rgba(223,194,120,.92);
}
.chart-canvas-wrap{
  padding:12px 16px 20px;
  position:relative;
  min-height:280px;
  max-height:340px;
}
.chart-canvas-wrap.tall{min-height:300px}
.chart-note{
  padding:12px 20px 16px;
  font-size:12px;color:var(--muted2);
  border-top:1px solid rgba(200,168,75,.06);
}
.dash-chart-row-full{
  display:grid;
  grid-template-columns:1fr;
  gap:18px;
  margin-bottom:22px;
}
.chart-card-wide .chart-canvas-wrap{min-height:220px;max-height:260px}

.dash-split{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:18px;
  margin-bottom:8px;
}
@media(max-width:960px){.dash-split{grid-template-columns:1fr}}

.dash-panel{background:var(--panel);border:1px solid var(--border);border-radius:10px;overflow:hidden;min-height:0}
.dash-panel-head{
  padding:14px 20px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  flex-wrap:wrap;
}
.dash-panel-title{
  font-family:var(--fc);font-size:9.5px;letter-spacing:.2em;text-transform:uppercase;
  color:rgba(200,168,75,.48);
}
.dash-panel-hint{font-size:12px;color:var(--muted2)}
.leader-list{list-style:none;margin:0;padding:0}
.leader-row{
  display:flex;align-items:center;gap:14px;padding:13px 20px;
  border-bottom:1px solid rgba(200,168,75,.06);
  transition:background .12s;
}
.leader-row:last-child{border-bottom:none}
.leader-row:hover{background:rgba(200,168,75,.04)}
.leader-pos{
  width:28px;height:28px;border-radius:8px;
  background:rgba(200,168,75,.09);border:1px solid rgba(200,168,75,.14);
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;font-family:var(--fc);font-size:11px;font-weight:600;color:var(--gold);
}
.leader-body{flex:1;min-width:0}
.leader-body a{display:block;color:var(--text);font-weight:500;font-size:13.8px;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.leader-body a:hover{color:var(--gold-lt)}
.leader-cat{font-size:11px;color:var(--muted2);margin-top:2px}
.leader-stat{font-family:var(--ff);font-size:clamp(22px,2.5vw,26px);font-weight:300;color:var(--gold-lt);line-height:1;flex-shrink:0}
.leader-stat span{display:block;font-size:10px;font-family:var(--fu);color:var(--muted2);margin-top:3px;text-align:right}
.fb-mini{padding:13px 20px;border-bottom:1px solid rgba(200,168,75,.06)}
.fb-mini:last-child{border-bottom:none}
.fb-mini-head{display:flex;justify-content:space-between;gap:12px;margin-bottom:5px;font-size:12.5px}
.fb-mini-head a{font-weight:500;color:var(--text);text-decoration:none}
.fb-mini-head a:hover{color:var(--gold-lt)}
.fb-mini-stars{color:var(--gold);font-size:12px}
.fb-mini-msg{font-size:12.8px;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.fb-mini-foot{font-size:11px;color:var(--muted2);margin-top:6px}
.dash-empty{padding:38px 20px;text-align:center;font-size:13px;color:var(--muted2);line-height:1.55}
</style>
@endpush

@section('content')

<div class="page-head">
  <div class="page-head-left">
    <h1>Tableau de bord</h1>
    <p>Vue d’ensemble des consultations, des avis et de l’activité autour des expositions numériques.</p>
  </div>
</div>

<div class="stat-row">
  <div class="stat-card">
    <div class="stat-card-icon"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
    <div class="stat-card-num">{{ $stats['total_artifacts'] ?? 0 }}</div>
    <div class="stat-card-label">Artefacts référencés</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 9l-6 6-3-3-4 4"/></svg></div>
    <div class="stat-card-num">{{ number_format((int) ($stats['total_scans'] ?? 0), 0, ',', ' ') }}</div>
    <div class="stat-card-label">Scans QR (consultations)</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="M12 8v4"/><circle cx="12" cy="16" r=".5" fill="currentColor"/></svg></div>
    <div class="stat-card-num">{{ $stats['with_3d'] ?? 0 }}</div>
    <div class="stat-card-label">Fiches avec modèle 3D</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon"><svg viewBox="0 0 24 24"><path d="M12 17.75l-6.172 3.848 1.639-7.052L2 9.798l7.229-.596L12 2.75l2.771 6.452 7.229.596-5.467 4.948 1.639 7.052z"/></svg></div>
    <div class="stat-card-num">{{ ($stats['avg_rating'] ?? null) !== null ? number_format($stats['avg_rating'], 1, ',', ' ') : '—' }}</div>
    <div class="stat-card-label">
      Note moyenne
      @if(($stats['feedback_count'] ?? 0) > 0)
      <span style="opacity:.85">({{ $stats['feedback_count'] }} avis)</span>
      @endif
    </div>
  </div>
</div>

<div class="dash-chart-grid">
  <div class="chart-card">
    <div class="chart-card-head">
      <span class="chart-card-title">Répartition des avis</span>
      <span class="chart-card-sub">Notes visiteurs (1 à 5)</span>
    </div>
    <div class="chart-canvas-wrap">
      <canvas id="chartRatings" aria-label="Graphique des notes"></canvas>
    </div>
    <p class="chart-note">Chaque segment correspond au nombre d’avis publiés pour cette note.</p>
  </div>
  <div class="chart-card">
    <div class="chart-card-head">
      <span class="chart-card-title">Évolution des scans</span>
      <span class="chart-card-sub">Consultations QR sur 12 mois</span>
    </div>
    <div class="chart-canvas-wrap tall">
      <canvas id="chartScans" aria-label="Évolution mensuelle des scans"></canvas>
    </div>
    <p class="chart-note">Basé sur la date d’enregistrement de chaque visite (table des scans).</p>
  </div>
</div>

<div class="dash-chart-row-full">
  <div class="chart-card chart-card-wide">
    <div class="chart-card-head">
      <span class="chart-card-title">Scans par catégorie</span>
      <span class="chart-card-sub">Segments thématiques les plus sollicités</span>
    </div>
    <div class="chart-canvas-wrap">
      <canvas id="chartCategories" aria-label="Scans par catégorie"></canvas>
    </div>
  </div>
</div>

<div class="dash-split">
  <div class="dash-panel">
    <div class="dash-panel-head">
      <div>
        <div class="dash-panel-title">Artefacts les plus consultés</div>
        <div class="dash-panel-hint">Classement par nombre de scans</div>
      </div>
    </div>
    @if($topScanned->isEmpty())
      <div class="dash-empty">Aucune consultation enregistrée pour le moment.</div>
    @else
      <ul class="leader-list">
        @foreach($topScanned as $row)
        <li class="leader-row">
          <span class="leader-pos">{{ $loop->iteration }}</span>
          <div class="leader-body">
            <a href="{{ route('admin.artifacts.edit', $row) }}" title="{{ $row->name }}">{{ $row->name }}</a>
            <div class="leader-cat">{{ $row->category?->name ?? 'Sans catégorie' }}</div>
          </div>
          <div class="leader-stat">
            {{ number_format((int) $row->visits_count, 0, ',', ' ') }}
            <span>scans</span>
          </div>
        </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="dash-panel">
    <div class="dash-panel-head">
      <div>
        <div class="dash-panel-title">Derniers avis</div>
        <div class="dash-panel-hint"><a href="{{ route('admin.feedbacks.index') }}" style="color:var(--gold);text-decoration:none">Voir tous les avis →</a></div>
      </div>
    </div>
    @if($recentFeedbacks->isEmpty())
      <div class="dash-empty">Pas encore de retour visiteur.</div>
    @else
      @foreach($recentFeedbacks as $rf)
        <div class="fb-mini">
          <div class="fb-mini-head">
            <a href="{{ route('admin.artifacts.edit', $rf->artifact) }}">{{ Str::limit($rf->artifact?->name ?? '—', 42) }}</a>
            <span class="fb-mini-stars">{{ str_repeat('★', (int) $rf->rating) }}{{ str_repeat('☆', max(0, 5 - (int) $rf->rating)) }}</span>
          </div>
          @if($rf->comment)
            <div class="fb-mini-msg">{{ $rf->comment }}</div>
          @endif
          <div class="fb-mini-foot">
            {{ $rf->visitor_name ? $rf->visitor_name.' · ' : '' }}{{ $rf->created_at?->diffForHumans() }}
          </div>
        </div>
      @endforeach
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  if (typeof Chart === 'undefined') return;
  Chart.defaults.color = 'rgba(232,228,217,0.55)';
  Chart.defaults.borderColor = 'rgba(200,168,75,0.12)';
  Chart.defaults.font.family = "'Source Sans 3', system-ui, sans-serif";

  var gold = '#c8a84b';
  var goldLt = '#dfc278';
  var goldDk = '#a8842e';
  var ratingLabels = @json($ratingLabels);
  var ratingValues = @json($ratingValues);
  var monthLabels = @json($chartMonths);
  var scanValues = @json($chartScanValues);
  var catRows = @json($categoryScans->values());
  var catLabels = catRows.map(function(r){ return r.cat_name; });
  var catData = catRows.map(function(r){ return r.c; });

  var donutEl = document.getElementById('chartRatings');
  var totalRatings = ratingValues.reduce(function(a,b){ return a+b; }, 0);
  if (donutEl) {
    if (totalRatings === 0) {
      donutEl.parentElement.innerHTML = '<div class="dash-empty" style="min-height:200px;display:flex;align-items:center;justify-content:center;padding:28px;">Aucun avis enregistré — le graphique s’affichera dès la première notation.</div>';
    } else {
      new Chart(donutEl, {
        type: 'doughnut',
        data: {
          labels: ratingLabels,
          datasets: [{
            data: ratingValues,
            backgroundColor: [
              'rgba(176,52,52,0.55)',
              'rgba(217,119,6,0.55)',
              'rgba(202,167,72,0.5)',
              'rgba(148,174,112,0.55)',
              'rgba(93,157,132,0.6)'
            ],
            borderColor: '#0e1f10',
            borderWidth: 2,
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '62%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 14, boxWidth: 10, boxHeight: 10, usePointStyle: true }
            },
            tooltip: {
              callbacks: {
                label: function(ctx) {
                  var v = ctx.raw || 0;
                  var p = totalRatings ? Math.round(v / totalRatings * 100) : 0;
                  return ' ' + v + ' avis (' + p + ' %)';
                }
              }
            }
          }
        },
        plugins: [{
          id: 'centreTotal',
          afterDraw: function(chart) {
            var ctx = chart.ctx;
            var meta = chart.getDatasetMeta(0);
            if (!meta || !chart.chartArea) return;
            var cx = (chart.chartArea.left + chart.chartArea.right) / 2;
            var cy = (chart.chartArea.top + chart.chartArea.bottom) / 2;
            ctx.save();
            ctx.font = '600 22px "Cormorant Garamond", Georgia, serif';
            ctx.fillStyle = goldLt;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(String(totalRatings), cx, cy - 8);
            ctx.font = '10px "Cinzel", serif';
            ctx.fillStyle = 'rgba(200,168,75,0.45)';
            ctx.fillText('AVIS', cx, cy + 14);
            ctx.restore();
          }
        }]
      });
    }
  }

  var lineEl = document.getElementById('chartScans');
  if (lineEl) {
    new Chart(lineEl, {
      type: 'line',
      data: {
        labels: monthLabels,
        datasets: [{
          label: 'Scans',
          data: scanValues,
          borderColor: gold,
          backgroundColor: 'rgba(200,168,75,0.08)',
          borderWidth: 2,
          tension: 0.35,
          fill: true,
          pointRadius: 4,
          pointBackgroundColor: goldLt,
          pointBorderColor: goldDk
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: { color: 'rgba(200,168,75,0.06)' },
            ticks: { maxRotation: 0, autoSkip: true }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(200,168,75,0.06)' },
            ticks: { stepSize: 1, precision: 0 }
          }
        },
        plugins: { legend: { display: false } }
      }
    });
  }

  var barEl = document.getElementById('chartCategories');
  if (barEl) {
    if (!catLabels.length) {
      barEl.parentElement.innerHTML = '<div class="dash-empty" style="min-height:180px;display:flex;align-items:center;justify-content:center;">Pas encore de données par catégorie.</div>';
    } else {
      new Chart(barEl, {
        type: 'bar',
        data: {
          labels: catLabels,
          datasets: [{
            label: 'Scans',
            data: catData,
            backgroundColor: 'rgba(200,168,75,0.38)',
            borderColor: gold,
            borderWidth: 1,
            borderRadius: 6
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(200,168,75,0.06)' } },
            y: { grid: { display: false } }
          },
          plugins: { legend: { display: false } }
        }
      });
    }
  }
})();
</script>
@endpush
