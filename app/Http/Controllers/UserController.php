<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Country Dashboard
     *
     * Features:
     * - Country search
     * - Country ranking by posts/users
     * - Total users
     * - Total posts
     * - Post percentage
     * - Top contributor
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'posts');

        $totalPosts = Post::count();

        $countriesQuery = Country::withCount([
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
        ]);

        /**
         * Country search.
         */
        if (!empty($search)) {
            $countriesQuery->where('name', 'like', '%' . $search . '%');
        }

        /**
         * Country ranking.
         *
         * posts = rank by total posts
         * users = rank by total users
         */
        if ($sort === 'users') {
            $countriesQuery
                ->orderByDesc('users_count')
                ->orderBy('name');
        } else {
            $countriesQuery
                ->orderByDesc('posts_count')
                ->orderBy('name');
        }

        $countries = $countriesQuery->get();

        /**
         * Add analytics.
         */
        $countries->each(function ($country) use ($totalPosts) {

            $country->post_percentage = $totalPosts > 0
                ? round(
                    ($country->posts_count / $totalPosts) * 100,
                    1
                )
                : 0;

            $country->top_user = $country->users->first();

            $country->top_user_posts = $country->top_user
                ? $country->top_user->posts_count
                : 0;
        });

        return view('country-posts', compact(
            'countries',
            'totalPosts',
            'search',
            'sort'
        ));
    }


    /**
     * Country Details
     *
     * Features:
     * - Post search
     * - Date range filtering
     * - Pagination
     * - User contribution percentage
     * - Growth analytics
     * - Most active user
     * - Monthly chart
     * - Post details modal
     */
    public function countryPosts(Request $request, $countryId)
    {
        $country = Country::withCount([
            'users',
            'posts',
        ])->findOrFail($countryId);

        /**
         * Filters.
         */
        $search = $request->input('search');

        $fromDate = $request->input('from_date');

        $toDate = $request->input('to_date');

        /**
         * Main hasManyThrough query.
         *
         * Country → Users → Posts
         */
        $postsQuery = $country->posts()
            ->with('user')
            ->latest();

        /**
         * Search post name.
         */
        if (!empty($search)) {
            $postsQuery->where(
                'posts.name',
                'like',
                '%' . $search . '%'
            );
        }

        /**
         * From date filter.
         */
        if (!empty($fromDate)) {
            $postsQuery->whereDate(
                'posts.created_at',
                '>=',
                $fromDate
            );
        }

        /**
         * To date filter.
         */
        if (!empty($toDate)) {
            $postsQuery->whereDate(
                'posts.created_at',
                '<=',
                $toDate
            );
        }

        /**
         * Pagination.
         */
        $posts = $postsQuery
            ->paginate(5)
            ->withQueryString();

        /**
         * Users and contribution percentage.
         */
        $users = $country->users()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->get();

        /**
         * Calculate user contribution percentage.
         */
        $users->each(function ($user) use ($country) {

            $user->contribution_percentage =
                $country->posts_count > 0
                    ? round(
                        ($user->posts_count / $country->posts_count) * 100,
                        1
                    )
                    : 0;
        });

        /**
         * Most active user.
         */
        $mostActiveUser = $users->first();

        /**
         * Current month.
         */
        $currentMonthStart = Carbon::now()
            ->startOfMonth();

        $currentMonthEnd = Carbon::now()
            ->endOfMonth();

        /**
         * Previous month.
         */
        $previousMonthStart = Carbon::now()
            ->subMonth()
            ->startOfMonth();

        $previousMonthEnd = Carbon::now()
            ->subMonth()
            ->endOfMonth();

        /**
         * Current month posts.
         */
        $currentMonthPosts = $country->posts()
            ->whereBetween(
                'posts.created_at',
                [
                    $currentMonthStart,
                    $currentMonthEnd,
                ]
            )
            ->count();

        /**
         * Previous month posts.
         */
        $previousMonthPosts = $country->posts()
            ->whereBetween(
                'posts.created_at',
                [
                    $previousMonthStart,
                    $previousMonthEnd,
                ]
            )
            ->count();

        /**
         * Growth percentage.
         */
        if ($previousMonthPosts > 0) {

            $growthPercentage = round(
                (
                    ($currentMonthPosts - $previousMonthPosts)
                    / $previousMonthPosts
                ) * 100,
                1
            );

        } elseif ($currentMonthPosts > 0) {

            $growthPercentage = 100;

        } else {

            $growthPercentage = 0;
        }

        /**
         * Monthly activity chart.
         *
         * Last 12 months.
         */
        $chartLabels = [];

        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {

            $month = Carbon::now()
                ->subMonths($i);

            $start = $month->copy()->startOfMonth();

            $end = $month->copy()->endOfMonth();

            $count = $country->posts()
                ->whereBetween(
                    'posts.created_at',
                    [$start, $end]
                )
                ->count();

            $chartLabels[] = $month->format('M Y');

            $chartData[] = $count;
        }

        /**
         * Return page.
         */
        return view('country-details', compact(
            'country',
            'posts',
            'users',
            'search',
            'fromDate',
            'toDate',
            'mostActiveUser',
            'currentMonthPosts',
            'previousMonthPosts',
            'growthPercentage',
            'chartLabels',
            'chartData'
        ));
    }


    /**
     * CSV Export
     *
     * Exports selected country's post report.
     *
     * Includes:
     * - Post
     * - Author
     * - Country
     * - Created date
     */
    public function exportCsv(Request $request, $countryId)
    {
        $country = Country::findOrFail($countryId);

        $search = $request->input('search');

        $fromDate = $request->input('from_date');

        $toDate = $request->input('to_date');

        /**
         * Same filtering used by the page.
         */
        $postsQuery = $country->posts()
            ->with('user')
            ->latest();

        /**
         * Search.
         */
        if (!empty($search)) {
            $postsQuery->where(
                'posts.name',
                'like',
                '%' . $search . '%'
            );
        }

        /**
         * From date.
         */
        if (!empty($fromDate)) {
            $postsQuery->whereDate(
                'posts.created_at',
                '>=',
                $fromDate
            );
        }

        /**
         * To date.
         */
        if (!empty($toDate)) {
            $postsQuery->whereDate(
                'posts.created_at',
                '<=',
                $toDate
            );
        }

        $posts = $postsQuery->get();

        /**
         * CSV filename.
         */
        $filename = strtolower(
            str_replace(
                ' ',
                '-',
                $country->name
            )
        ) . '-post-report.csv';

        /**
         * Stream CSV.
         */
        return response()->streamDownload(
            function () use ($posts, $country) {

                $file = fopen('php://output', 'w');

                /**
                 * Header.
                 */
                fputcsv($file, [
                    'Post ID',
                    'Post Name',
                    'Author',
                    'Author Email',
                    'Country',
                    'Created Date',
                ]);

                /**
                 * Rows.
                 */
                foreach ($posts as $post) {

                    fputcsv($file, [
                        $post->id,
                        $post->name,
                        $post->user->name ?? 'N/A',
                        $post->user->email ?? 'N/A',
                        $country->name,
                        $post->created_at
                            ? $post->created_at->format('Y-m-d H:i:s')
                            : '',
                    ]);
                }

                fclose($file);

            },
            $filename,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }
}