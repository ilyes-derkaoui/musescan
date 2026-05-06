<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Feedback;
use App\Models\Visit;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $avgRating = Feedback::avg('rating');

        $stats = [
            'total_artifacts' => Artifact::count(),
            'total_scans' => Visit::count(),
            'with_3d' => Artifact::where('has_3d_model', true)->count(),
            'feedback_count' => Feedback::count(),
            'avg_rating' => $avgRating !== null ? round((float) $avgRating, 2) : null,
        ];

        $topScanned = Artifact::query()
            ->with('category')
            ->withCount('visits')
            ->orderByDesc('visits_count')
            ->limit(8)
            ->get();

        $recentFeedbacks = Feedback::query()
            ->with('artifact')
            ->latest()
            ->limit(5)
            ->get();

        $since = Carbon::now()->subMonths(11)->startOfMonth();
        $visitCountsByMonth = Visit::query()
            ->where('scanned_at', '>=', $since)
            ->get(['scanned_at'])
            ->groupBy(fn ($v) => $v->scanned_at->format('Y-m'))
            ->map->count();

        $chartMonths = [];
        $chartScanValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i)->startOfMonth();
            $key = $d->format('Y-m');
            $chartMonths[] = $d->locale('fr')->translatedFormat('M');
            $chartScanValues[] = (int) ($visitCountsByMonth[$key] ?? 0);
        }

        $ratingDist = Feedback::query()
            ->selectRaw('rating, COUNT(*) as cnt')
            ->groupBy('rating')
            ->pluck('cnt', 'rating');

        $ratingLabels = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★'];
        $ratingValues = [];
        for ($s = 1; $s <= 5; $s++) {
            $ratingValues[] = (int) ($ratingDist[$s] ?? 0);
        }

        $categoryScans = Visit::query()
            ->join('artifacts', 'artifacts.id', '=', 'visits.artifact_id')
            ->leftJoin('categories', 'categories.id', '=', 'artifacts.category_id')
            ->select(['categories.id', 'categories.name'])
            ->selectRaw('COUNT(visits.id) as c')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('c')
            ->limit(7)
            ->get()
            ->map(function ($row) {
                return (object) [
                    'cat_name' => $row->name ?: 'Sans catégorie',
                    'c' => (int) $row->c,
                ];
            });

        return view('admin.statistics.index', compact(
            'stats',
            'topScanned',
            'recentFeedbacks',
            'chartMonths',
            'chartScanValues',
            'ratingLabels',
            'ratingValues',
            'categoryScans'
        ));
    }
}
