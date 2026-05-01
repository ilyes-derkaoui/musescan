<?php

use App\Http\Controllers\Api\ArtifactLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/artifacts/by-qr/{qrCode}', [ArtifactLookupController::class, 'showByQr']);
