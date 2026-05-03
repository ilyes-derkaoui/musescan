<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artifact;
use App\Models\ArtifactMedia;
use App\Models\Category;
use App\Support\QrCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArtifactController extends Controller
{
    private array $locales = ['ar', 'es', 'fr', 'en', 'zh', 'ru'];

    public function index(): View
    {
        $artifacts = Artifact::with('category')->orderBy('id', 'desc')->paginate(10);

        return view('admin.artifacts.index', compact('artifacts'));
    }

    public function create(): View
    {
        $artifact = new Artifact();
        $categories = Category::orderBy('name')->get();

        return view('admin.artifacts.form', [
            'artifact' => $artifact,
            'categories' => $categories,
            'translations' => [],
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateArtifact($request);
        $validated['qr_image_path'] = QrCodeGenerator::generateForArtifact($validated['qr_code']);
        $artifact = Artifact::create($validated);
        $this->saveTranslationsAndModel($artifact, $request);

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact created.');
    }

    public function edit(Artifact $artifact): View
    {
        $artifact->load(['translations', 'media']);
        $categories = Category::orderBy('name')->get();
        $translations = $artifact->translations->keyBy('locale');

        return view('admin.artifacts.form', [
            'artifact' => $artifact,
            'categories' => $categories,
            'translations' => $translations,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Artifact $artifact): RedirectResponse
    {
        $validated = $this->validateArtifact($request);
        $qrChanged = $artifact->qr_code !== $validated['qr_code'];

        if ($qrChanged) {
            if ($artifact->qr_image_path) {
                Storage::disk('public')->delete($artifact->qr_image_path);
            }
            $validated['qr_image_path'] = QrCodeGenerator::generateForArtifact($validated['qr_code']);
        }

        $artifact->update($validated);
        $this->saveTranslationsAndModel($artifact, $request);

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact updated.');
    }

    public function destroy(Artifact $artifact): RedirectResponse
    {
        if ($artifact->qr_image_path) {
            Storage::disk('public')->delete($artifact->qr_image_path);
        }

        $artifact->delete();

        return redirect()->route('admin.artifacts.index')->with('success', 'Artifact deleted.');
    }

    private function validateArtifact(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'floor' => ['required', 'integer', 'min:1', 'max:9'],
            'category_id' => ['required', 'exists:categories,id'],
            'qr_code' => ['required', 'string', 'max:255'],
            'has_3d_model' => ['nullable', 'boolean'],
        ]);

        $validated['has_3d_model'] = $request->boolean('has_3d_model');

        return $validated;
    }

    private function saveTranslationsAndModel(Artifact $artifact, Request $request): void
    {
        foreach ($this->locales as $locale) {
            $title = trim((string) $request->input("translations.$locale.name"));
            $description = trim((string) $request->input("translations.$locale.description"));

            if ($title === '' || $description === '') {
                continue;
            }

            $artifact->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $title, 'description' => $description]
            );
        }

        $modelPath = trim((string) $request->input('model_3d_path'));

        if ($modelPath !== '') {
            ArtifactMedia::updateOrCreate(
                ['artifact_id' => $artifact->id, 'type' => 'model_3d', 'is_main' => true],
                ['file_path' => $modelPath]
            );
        } else {
            ArtifactMedia::where('artifact_id', $artifact->id)
                ->where('type', 'model_3d')
                ->where('is_main', true)
                ->delete();
        }
    }

    public function show(string $qr_code)
{
    $artifact = Artifact::where('qr_code', $qr_code)
        ->with(['category', 'media', 'visits'])
        ->firstOrFail();

    // Traductions indexées par locale
    $translations = $artifact->translations->keyBy('locale');

    // Médias séparés par type
    $images  = $artifact->media->where('type', 'image');
    $audio   = $artifact->media->where('type', 'audio')->first();
    $model3d = $artifact->media->where('type', 'model_3d')->first();

    // Personnage lié (optionnel)
    $figure          = $artifact->historicalFigure ?? null;
    $figureArtifacts = $figure?->artifacts->where('id', '!=', $artifact->id);

    // Feedbacks paginés
    $feedbacks = $artifact->feedbacks()
        ->latest()
        ->paginate(5);

    return view('artifacts.show', compact(
        'artifact', 'translations', 'images',
        'audio', 'model3d', 'figure',
        'figureArtifacts', 'feedbacks'
    ));
}

public function logVisit(string $qr_code)
{
    $artifact = Artifact::where('qr_code', $qr_code)->firstOrFail();
    $artifact->visits()->create([
        'ip_hash'    => hash('sha256', request()->ip()),
        'scanned_at' => now(),
    ]);
    return response()->json(['ok' => true]);
}

public function storeFeedback(Request $request, string $qr_code)
{
    $artifact = Artifact::where('qr_code', $qr_code)->firstOrFail();

    $request->validate([
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    $artifact->feedbacks()->create([
        'visitor_name' => $request->visitor_name,
        'rating'       => $request->rating,
        'comment'      => $request->comment,
    ]);

    return back()->with('feedback_success', true);
}


}
