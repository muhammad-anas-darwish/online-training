<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('roles', RoleController::class);
Route::get('permissions', [RoleController::class,'getAllPermissions']);