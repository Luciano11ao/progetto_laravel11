<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Note\APINoteController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Maia\CommissionController;
use App\Http\Controllers\Maia\ServiceController;
use App\Http\Controllers\Maia\AssetClassController;

Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::middleware('auth:sanctum')->group(function () {
    // Notes
    Route::get('/notes', [APINoteController::class, 'index']);
    Route::post('/notes', [APINoteController::class, 'store']);
    Route::put('/notes/{id}', [APINoteController::class, 'update']);
    Route::delete('/notes/{id}', [APINoteController::class, 'destroy']);
    Route::post('/notes/import', [APINoteController::class, 'importNotes']);
    Route::post('/notes/importExcel', [APINoteController::class, 'importNotesExcel']);

    // AssetClass
    Route::get('/asset_classes', [AssetClassController::class, 'index']);
    Route::post('/asset_classes', [AssetClassController::class, 'store']);
    Route::get('/asset_classes/{id}', [AssetClassController::class, 'show']);
    Route::put('/asset_classes/{id}', [AssetClassController::class, 'update']);
    Route::delete('/asset_classes/{id}', [AssetClassController::class, 'destroy']);

    // Service
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

    // Commission
    Route::get('/commissions', [CommissionController::class, 'index']);
    Route::post('/commissions', [CommissionController::class, 'store']);
    Route::get('/commissions/{id}', [CommissionController::class, 'show']);
    Route::put('/commissions/{id}', [CommissionController::class, 'update']);
    Route::delete('/commissions/{id}', [CommissionController::class, 'destroy']);

    // Esercizio di Riccardo (filter)
    Route::get('asset-classes', [AssetClassController::class, 'getAssetClasses']);
});
