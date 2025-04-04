<?php

use App\Livewire\BankStatement;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Существующие маршруты Breeze
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Ваши восстановленные маршруты
    // Route::get('/bank', BankStatement::class)->name('bank');

    Route::get('download/export/{filename}', function ($filename) {
        $path = storage_path('app' . DIRECTORY_SEPARATOR . 'exports' . DIRECTORY_SEPARATOR . $filename);

        if (file_exists($path)) {
            return response()->download($path);
        }

        return abort(404, 'Файл не найден');
    });

    Route::redirect('/', 'bank');

    Route::view('bank', 'deposit-statement')
        ->middleware(['auth', 'verified'])
        ->name('bank');

    Route::view('dashboard', 'dashboard')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');


    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

        Route::view('logs', 'logs')
        ->middleware(['auth', 'verified'])
        ->name('logs');
});
require __DIR__ . '/auth.php';
