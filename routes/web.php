<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;


Route::get('/',        [PhotoController::class, 'index']);   // camera page
Route::post('/upload', [PhotoController::class, 'store']);   // save photo
Route::get('/result',  [PhotoController::class, 'result']);  // result page