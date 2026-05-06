<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArtifactController;
use App\Http\Controllers\Api\ArtifactLookupController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\AdminAuthController;

// ── Public: Artifacts ─────────────────────────────────────
Route::get('/artifacts', [ArtifactController::class, 'index']);
Route::get('/artifacts/{id}', [ArtifactController::class, 'show']);
Route::get('/artifacts/by-qr/{qrCode}', [ArtifactLookupController::class, 'showByQr']);

// ── Public: Scan recording ────────────────────────────────
Route::post('/scan', [ScanController::class, 'store']);

// ── Public: Feedback ──────────────────────────────────────
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/feedback', [FeedbackController::class, 'index']);

// ── Public: Stats ─────────────────────────────────────────
Route::get('/stats', [StatsController::class, 'index']);

// ── Public: Chatbot ───────────────────────────────────────
Route::post('/chatbot', [ChatbotController::class, 'ask']);

// ── Admin: Auth ───────────────────────────────────────────
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// ── Admin: Artifact CRUD (protected) ─────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/artifacts', [ArtifactController::class, 'store']);
    Route::put('/artifacts/{id}', [ArtifactController::class, 'update']);
    Route::delete('/artifacts/{id}', [ArtifactController::class, 'destroy']);
});

