<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VideoController;

Route::get('/upload', [VideoController::class, 'showUploadForm']);
Route::post('/upload', [VideoController::class, 'upload']);
Route::get('/videos', [VideoController::class, 'index']);

