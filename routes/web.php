<?php

use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\CredentialVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => ['Laravel' => app()->version()]);

Route::get('verify/{uuid}', CredentialVerificationController::class)->name('credentials.verify');

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');
