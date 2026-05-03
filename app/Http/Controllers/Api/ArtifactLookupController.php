<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use Illuminate\Http\JsonResponse;

class ArtifactLookupController extends Controller
{
    private function toPublicMediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    public function showByQr(string $qrCode): JsonResponse
    {
        $artifact = Artifact::with(['category', 'translations', 'media'])
            ->where('qr_code', $qrCode)
            ->first();

        if (! $artifact) {
            return response()->json([
                'message' => 'Artifact not found',
            ], 404);
        }

        $translations = $artifact->translations->keyBy('locale');
        $modelMedia = $artifact->media
            ->where('type', 'model_3d')
            ->sortByDesc('is_main')
            ->first();
        $imageMedia = $artifact->media
            ->where('type', 'image')
            ->sortByDesc('is_main')
            ->values();
        $mainImage = $imageMedia->first();

        $mainTitle = optional($translations->get('fr'))->name
            ?? optional($translations->first())->name
            ?? $artifact->name;

        $mainDesc = optional($translations->get('fr'))->description
            ?? optional($translations->first())->description
            ?? 'Description not available.';

        return response()->json([
            'id' => $artifact->id,
            'qrCode' => $artifact->qr_code,
            'qrImage' => $artifact->qr_image_path ? asset('storage/' . $artifact->qr_image_path) : null,
            'modelSrc' => $modelMedia?->file_path ?? '',
            'mainImage' => $mainImage ? $this->toPublicMediaUrl((string) $mainImage->file_path) : null,
            'galleryImages' => $imageMedia->map(fn ($img) => $this->toPublicMediaUrl((string) $img->file_path))->all(),
            'category' => $artifact->category?->name ?? 'Collection du musee',
            'ref' => strtoupper($artifact->qr_code ?? ('artifact-' . $artifact->id)),
            'epoque' => 'Niveau ' . $artifact->floor,
            'section' => $artifact->category?->name ?? 'Section principale',
            'ar' => [
                'title' => optional($translations->get('ar'))->name ?? $mainTitle,
                'desc' => optional($translations->get('ar'))->description ?? $mainDesc,
            ],
            'es' => [
                'title' => optional($translations->get('es'))->name ?? $mainTitle,
                'desc' => optional($translations->get('es'))->description ?? $mainDesc,
            ],
            'fr' => [
                'title' => optional($translations->get('fr'))->name ?? $mainTitle,
                'desc' => optional($translations->get('fr'))->description ?? $mainDesc,
            ],
            'en' => [
                'title' => optional($translations->get('en'))->name ?? $mainTitle,
                'desc' => optional($translations->get('en'))->description ?? $mainDesc,
            ],
            'zh' => [
                'title' => optional($translations->get('zh'))->name ?? $mainTitle,
                'desc' => optional($translations->get('zh'))->description ?? $mainDesc,
            ],
            'ru' => [
                'title' => optional($translations->get('ru'))->name ?? $mainTitle,
                'desc' => optional($translations->get('ru'))->description ?? $mainDesc,
            ],
        ]);
    }
}
