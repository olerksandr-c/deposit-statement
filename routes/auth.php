<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {


    Volt::route('login', 'pages.auth.login')
        ->name('login');
});

Route::middleware('auth')->group(function () {

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->middleware('role:administrator')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->middleware('role:administrator')
        ->name('password.reset');

    Volt::route('register', 'pages.auth.register')
        ->middleware('role:administrator')
        ->name('register');
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1', 'role:administrator'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->middleware('role:administrator')
        ->name('password.confirm');
});
