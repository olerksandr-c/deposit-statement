<?php

use App\Livewire\BankStatement;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bank', BankStatement::class);

Route::get('download/export/{filename}', function ($filename) {
    $path = storage_path('app' . DIRECTORY_SEPARATOR . 'exports' . DIRECTORY_SEPARATOR . $filename);

    if (file_exists($path)) {
        return response()->download($path);
    }

    return abort(404, 'Файл не найден');
});



