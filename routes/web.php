<?php

use App\Http\Controllers\Admin\ArtifactController;
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
    Route::resource('artifacts', ArtifactController::class)->except(['show']);
});


// Page publique — fiche artefact (scan QR)
Route::get('/artifact/{qr_code}', [ArtifactController::class, 'show'])
     ->name('artifacts.show');

// Enregistrer un scan (log visite)
Route::post('/artifact/{qr_code}/visit', [ArtifactController::class, 'logVisit'])
     ->name('artifacts.visit');

// Soumettre un feedback visiteur
Route::post('/artifact/{qr_code}/feedback', [ArtifactController::class, 'storeFeedback'])
     ->name('artifacts.feedback');
