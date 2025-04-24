<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\RoleController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('roles', RoleController::class);
Route::get('permissions', [RoleController::class,'getAllPermissions']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('test', function () {
        info(Auth::user());
        return 'test';
    });
});

Route::prefix('auth')->group(function () {
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:sanctum');
    
    // Add other auth routes as needed
});
