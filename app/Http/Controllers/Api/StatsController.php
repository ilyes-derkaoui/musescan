<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrScan;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $totalScans = QrScan::count();

        $topArtifacts = QrScan::select('artifact_id', DB::raw('COUNT(*) as scan_count'))
            ->with('artifact:id,name')
            ->groupBy('artifact_id')
            ->orderByDesc('scan_count')
            ->limit(5)
            ->get();

        $monthlyActivity = QrScan::selectRaw('MONTH(scan_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $languagesUsed = QrScan::select('language_used', DB::raw('COUNT(*) as count'))
            ->groupBy('language_used')
            ->get();

        return response()->json([
            'total_scans' => $totalScans,
            'top_artifacts' => $topArtifacts,
            'monthly_activity' => $monthlyActivity,
            'languages_used' => $languagesUsed,
        ]);
    }
}
