<?php

use Illuminate\Support\Facades\Route;
use Modules\Training\Http\Controllers\CategoryController;

Route::prefix('training')->middleware(['auth:sanctum'])->group(function () {
    // Training Categories Routes
    Route::apiResource('categories', CategoryController::class);
});
