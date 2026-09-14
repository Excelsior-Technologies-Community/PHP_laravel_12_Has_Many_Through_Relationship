<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/country-posts', [DashboardController::class, 'index'])
        ->name('country.posts');

    Route::get('/country/{country}/posts', [DashboardController::class, 'countryPosts'])
        ->name('country.posts.details');

    Route::resource('countries', CountryController::class);
    Route::resource('users', UserController::class);
    Route::resource('posts', PostController::class);

    Route::prefix('country/{country}/posts/export')->group(function () {
        Route::get('/csv', [DashboardController::class, 'exportCsv'])
            ->name('country.posts.export.csv');
        Route::get('/pdf', [DashboardController::class, 'exportPdf'])
            ->name('country.posts.export.pdf');
        Route::get('/excel', [DashboardController::class, 'exportExcel'])
            ->name('country.posts.export.excel');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
