<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Post;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Country Dashboard
     *
     * Shows:
     * - Total users per country
     * - Total posts per country
     * - Country post percentage
     * - Top contributing user
     * - Top user's post count
     */
    public function index()
    {
        /*
         * Get total posts across all countries.
         *
         * This is used to calculate each country's
         * percentage of total posts.
         */
        $totalPosts = Post::count();

        /*
         * Get all countries with:
         *
         * - User count
         * - Post count through hasManyThrough()
         * - Users with their individual post counts
         *
         * Users are sorted by post count so the first
         * user becomes the top contributor.
         */
        $countries = Country::withCount([
            'users',
            'posts',
        ])
        ->with([
            'users' => function ($query) {
                $query
                    ->withCount('posts')
                    ->orderByDesc('posts_count')
                    ->orderBy('name');
            },
        ])
        ->orderByDesc('posts_count')
        ->get();

        /*
         * Add analytics information to every country.
         */
        $countries->each(function ($country) use ($totalPosts) {

            /*
             * Calculate percentage of total posts.
             */
            $country->post_percentage = $totalPosts > 0
                ? round(($country->posts_count / $totalPosts) * 100, 1)
                : 0;

            /*
             * Get the top contributing user.
             */
            $country->top_user = $country->users->first();

            /*
             * Top user's post count.
             */
            $country->top_user_posts = $country->top_user
                ? $country->top_user->posts_count
                : 0;
        });

        return view('country-posts', compact(
            'countries',
            'totalPosts'
        ));
    }


    /**
     * Country-wise posts
     *
     * Features:
     * - Country selection
     * - Post search
     * - Pagination
     * - User post counts
     */
    public function countryPosts(Request $request, $countryId)
    {
        /*
         * Get selected country with:
         *
         * - Total users
         * - Total posts through hasManyThrough()
         */
        $country = Country::withCount([
            'users',
            'posts',
        ])->findOrFail($countryId);

        /*
         * Get search value.
         */
        $search = $request->input('search');

        /*
         * Get posts through:
         *
         * Country → Users → Posts
         *
         * This is the main hasManyThrough()
         * relationship.
         */
        $postsQuery = $country->posts()
            ->with('user')
            ->latest();

        /*
         * Search posts by name.
         */
        if (!empty($search)) {

            $postsQuery->where(
                'posts.name',
                'like',
                '%' . $search . '%'
            );
        }

        /*
         * Paginate five posts per page.
         *
         * withQueryString() keeps the search term
         * when moving between pagination pages.
         */
        $posts = $postsQuery
            ->paginate(5)
            ->withQueryString();

        /*
         * Get users belonging to this country
         * with their individual post counts.
         */
        $users = $country->users()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->get();

        return view('country-details', compact(
            'country',
            'posts',
            'users',
            'search'
        ));
    }
}