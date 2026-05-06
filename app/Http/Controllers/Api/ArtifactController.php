<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use Illuminate\Http\Request;

class ArtifactController extends Controller
{
    // GET /api/artifacts  — list all (with optional ?qr= search)
    public function index(Request $request)
    {
        $query = Artifact::with(['category', 'images']);

        if ($request->has('qr')) {
            $query->where('qr_code', $request->qr);
        }

        $artifacts = $query->withCount('qrScans as scan_count')->get();
        return response()->json($artifacts);
    }

    // GET /api/artifacts/{id}
    public function show($id)
    {
        $artifact = Artifact::with(['category', 'images'])
            ->withCount('qrScans as scan_count')
            ->findOrFail($id);
        return response()->json($artifact);
    }

    // POST /api/artifacts  (admin only)
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'qr_code' => 'required|string|unique:artifacts,qr_code',
        ]);

        $artifact = Artifact::create([
            'name'          => $request->name,
            'qr_code'       => $request->qr_code,
            'description'   => $request->description ?? '',
            'category_id'   => $request->category_id ?? 1,
            'has_3d'        => $request->has_3d ?? false,
            'floor'         => $request->floor ?? 1,
            'translations'  => $request->translations ?? [],
        ]);

        return response()->json($artifact, 201);
    }

    // PUT /api/artifacts/{id}  (admin only)
    public function update(Request $request, $id)
    {
        $artifact = Artifact::findOrFail($id);

        $artifact->update([
            'name'         => $request->name ?? $artifact->name,
            'qr_code'      => $request->qr_code ?? $artifact->qr_code,
            'description'  => $request->description ?? $artifact->description,
            'category_id'  => $request->category_id ?? $artifact->category_id,
            'has_3d'       => $request->has_3d ?? $artifact->has_3d,
            'floor'        => $request->floor ?? $artifact->floor,
            'translations' => $request->translations ?? $artifact->translations,
        ]);

        return response()->json($artifact);
    }

    // DELETE /api/artifacts/{id}  (admin only)
    public function destroy($id)
    {
        $artifact = Artifact::findOrFail($id);
        $artifact->delete();
        return response()->json(['message' => 'Artefact supprimé.'], 204);
    }
}
