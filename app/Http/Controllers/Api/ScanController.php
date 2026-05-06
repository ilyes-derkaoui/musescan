<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\QrScan;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'artifact_id' => 'required|exists:artifacts,id',
            'language' => 'required|string',
        ]);

        $artifact = Artifact::with(['category', 'images'])->findOrFail($request->artifact_id);

        $scan = QrScan::create([
            'artifact_id' => $request->artifact_id,
            'device_type' => $request->header('User-Agent'),
            'language_used' => $request->language,
        ]);

        return response()->json([
            'artifact' => $artifact,
            'scan' => $scan,
        ]);
    }
}
