<?php

use App\Http\Controllers\CredentialVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => ['Laravel' => app()->version()]);

Route::get('verify/{uuid}', CredentialVerificationController::class)->name('credentials.verify');
