<?php

use App\Http\Controllers\LdapController;
use App\Livewire\BankStatement;
use App\Livewire\RolePermissionManager;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {


    Route::get('download/export/{filename}', function ($filename) {
        $path = storage_path('app' . DIRECTORY_SEPARATOR . 'exports' . DIRECTORY_SEPARATOR . $filename);

        if (file_exists($path)) {
            return response()->download($path);
        }

        return abort(404, 'Файл не найден');
    });


    Route::redirect('/', 'bank');
    Route::redirect('/dashboard', 'bank');

    Route::view('bank', 'deposit-statement')
        ->middleware(['auth', 'verified'])
        ->name('bank');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    Route::view('logs', 'logs')
        ->middleware(['auth', 'verified'])
        ->name('logs');


    Route::get('roles-permissions', RolePermissionManager::class)
        ->middleware(['auth', 'verified'])
        ->name('role-permission-manager');
});

require __DIR__ . '/auth.php';
