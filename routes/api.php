<?php

use App\Http\Controllers\Api\CompleteGroupController;
use App\Http\Controllers\Api\CredentialPdfController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\GroupController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', fn(Request $request) => $request->user());

    Route::get('/test', function () {
        return response()->json([
            'message' => 'Test exitoso',
        ], 200);
    });

    Route::resource('courses', CourseController::class)->except('create', 'edit');
    Route::get('/courses/{id}/groups', [CourseController::class, 'getGroupsByCourse']);

    Route::resource('groups', GroupController::class)->except('create', 'edit');

    Route::post('groups/{group}/complete', CompleteGroupController::class)->name('groups.complete');
    Route::get('credentials/{credential}/pdf', CredentialPdfController::class)->name('credentials.pdf');
});
