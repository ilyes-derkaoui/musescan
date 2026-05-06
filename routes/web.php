<?php

use App\Http\Controllers\Admin\ArtifactController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Api\ArtifactLookupController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin.only'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [StatisticsController::class, 'index'])->name('dashboard');
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
    Route::resource('artifacts', ArtifactController::class)->except(['show']);

    // ── E: Printable QR sheet — one page per artifact or all at once ──────
    // Admin visits /admin/artifacts/print-qr → gets a printable A4 page
    // with all QR codes + artifact names, ready to cut and stick on display cases.
    Route::get('/artifacts/print-qr', [ArtifactController::class, 'printQr'])->name('artifacts.print-qr');
});

// ── A: Public JSON API used by the QR scanner in welcome.blade.php ────────────
// The JS variable ARTIFACT_LOOKUP_BASE is set to url('/api/artifacts/by-qr').
// When a QR is scanned, JS calls: GET /api/artifacts/by-qr/{qrCode}
// This was the MISSING route — without it every scan silently fell back to
// the hardcoded ARTIFACT_FALLBACK object and the database was never read.
Route::get('/api/artifacts/by-qr/{qrCode}', [ArtifactLookupController::class, 'showByQr'])
    ->name('api.artifacts.by-qr');

// Page publique — fiche artefact (scan QR)
Route::get('/artifact/{qr_code}', [ArtifactController::class, 'show'])
    ->name('artifacts.show');

// Enregistrer un scan (log visite)
Route::post('/artifact/{qr_code}/visit', [ArtifactController::class, 'logVisit'])
    ->name('artifacts.visit');

// Soumettre un feedback visiteur
Route::post('/artifact/{qr_code}/feedback', [ArtifactController::class, 'storeFeedback'])
    ->name('artifacts.feedback');
