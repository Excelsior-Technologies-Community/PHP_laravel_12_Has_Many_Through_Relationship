<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Has Many Through Relationship Routes
|--------------------------------------------------------------------------
*/

/*
 * Country statistics dashboard.
 */
Route::get('/country-posts', [UserController::class, 'index'])
    ->name('country.posts');

/*
 * Country-wise posts, search, pagination,
 * and users/posts details.
 */
Route::get('/country/{country}/posts', [UserController::class, 'countryPosts'])
    ->name('country.posts.details');