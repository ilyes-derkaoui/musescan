<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtifactLookupController extends Controller
{
    private function toPublicMediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    public function showByQr(string $qrCode, Request $request): JsonResponse
    {
        // Eager-load everything we need in one query.
        // withCount('visits') adds a visits_count attribute without a separate query.
        $artifact = Artifact::with(['category', 'translations', 'media'])
            ->withCount('visits')
            ->where('qr_code', $qrCode)
            ->first();

        if (! $artifact) {
            return response()->json([
                'message' => 'Artifact not found',
            ], 404);
        }

        // ── F: Log the scan visit ────────────────────────────────────────────
        // We hash the IP so we never store raw visitor IPs (privacy).
        // scanned_at is filled automatically by useCurrent() in the migration.
        // This is what feeds the dashboard's "Consultations QR sur 12 mois" chart.
        Visit::create([
            'artifact_id' => $artifact->id,
            'ip_hash'     => hash('sha256', $request->ip() . config('app.key')),
            'scanned_at'  => now(),
        ]);

        $translations = $artifact->translations->keyBy('locale');
        $modelMedia   = $artifact->media->where('type', 'model_3d')->sortByDesc('is_main')->first();
        $imageMedia   = $artifact->media->where('type', 'image')->sortByDesc('is_main')->values();
        $mainImage    = $imageMedia->first();

        $mainTitle = optional($translations->get('fr'))->name
            ?? optional($translations->first())->name
            ?? $artifact->name;

        $mainDesc = optional($translations->get('fr'))->description
            ?? optional($translations->first())->description
            ?? 'Description not available.';

        // ── H: Related artifacts — same category, different artifact ─────────
        // We pick up to 3 artifacts from the same category.
        // Each related item gets enough data for the frontend card:
        //   qrCode, title (in French as default), category, mainImage.
        // The visitor can tap a related card to open that artifact's overlay
        // without closing and rescanning.
        $related = [];
        if ($artifact->category_id) {
            $relatedArtifacts = Artifact::with(['translations', 'media'])
                ->where('category_id', $artifact->category_id)
                ->where('id', '!=', $artifact->id)
                ->limit(3)
                ->get();

            foreach ($relatedArtifacts as $rel) {
                $relTranslations = $rel->translations->keyBy('locale');
                $relTitle = optional($relTranslations->get('fr'))->name
                    ?? optional($relTranslations->first())->name
                    ?? $rel->name;
                $relImage = $rel->media->where('type', 'image')->sortByDesc('is_main')->first();

                $related[] = [
                    'qrCode'    => $rel->qr_code,
                    'title'     => $relTitle,
                    'category'  => $rel->category?->name ?? '',
                    'mainImage' => $relImage ? $this->toPublicMediaUrl((string) $relImage->file_path) : null,
                ];
            }
        }

        return response()->json([
            'id'           => $artifact->id,
            'qrCode'       => $artifact->qr_code,
            'qrImage'      => $artifact->qr_image_path ? asset('storage/'.$artifact->qr_image_path) : null,
            'modelSrc'     => $modelMedia ? $this->toPublicMediaUrl((string) $modelMedia->file_path) : '',
            'mainImage'    => $mainImage ? $this->toPublicMediaUrl((string) $mainImage->file_path) : null,
            'galleryImages'=> $imageMedia->map(fn ($img) => $this->toPublicMediaUrl((string) $img->file_path))->all(),
            'category'     => $artifact->category?->name ?? 'Collection du musée',
            'ref'          => strtoupper($artifact->qr_code ?? ('artifact-'.$artifact->id)),
            'epoque'       => 'Niveau '.$artifact->floor,
            'section'      => $artifact->category?->name ?? 'Section principale',
            // F: visits_count is added by withCount('visits') above
            'visitsCount'  => $artifact->visits_count,
            // H: related artifacts in same category
            'related'      => $related,
            'ar' => ['title' => optional($translations->get('ar'))->name ?? $mainTitle, 'desc' => optional($translations->get('ar'))->description ?? $mainDesc],
            'es' => ['title' => optional($translations->get('es'))->name ?? $mainTitle, 'desc' => optional($translations->get('es'))->description ?? $mainDesc],
            'fr' => ['title' => optional($translations->get('fr'))->name ?? $mainTitle, 'desc' => optional($translations->get('fr'))->description ?? $mainDesc],
            'en' => ['title' => optional($translations->get('en'))->name ?? $mainTitle, 'desc' => optional($translations->get('en'))->description ?? $mainDesc],
            'zh' => ['title' => optional($translations->get('zh'))->name ?? $mainTitle, 'desc' => optional($translations->get('zh'))->description ?? $mainDesc],
            'ru' => ['title' => optional($translations->get('ru'))->name ?? $mainTitle, 'desc' => optional($translations->get('ru'))->description ?? $mainDesc],
        ]);
    }
}
