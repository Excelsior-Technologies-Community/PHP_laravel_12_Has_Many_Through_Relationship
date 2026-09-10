<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Has Many Through Relationship Routes
|--------------------------------------------------------------------------
*/

/**
 * Country dashboard.
 *
 * Search countries
 * Country ranking
 * User/post statistics
 */
Route::get('/country-posts', [UserController::class, 'index'])
    ->name('country.posts');


/**
 * Country details.
 *
 * Search
 * Date filtering
 * Pagination
 * User contributions
 * Growth analytics
 * Most active user
 * Monthly chart
 * Post details
 */
Route::get(
    '/country/{country}/posts',
    [UserController::class, 'countryPosts']
)->name('country.posts.details');


/**
 * CSV export.
 */
Route::get(
    '/country/{country}/posts/export',
    [UserController::class, 'exportCsv']
)->name('country.posts.export');