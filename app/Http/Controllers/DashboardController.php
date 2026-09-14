<?php

namespace App\Http\Controllers;

use App\Exports\CountryReportExport;
use App\Models\Country;
use App\Models\Post;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'posts');

        $totalPosts = Post::count();
        $totalUsers = User::count();
        $totalCountries = Country::count();

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

        if (! empty($search)) {
            $countriesQuery->where('name', 'like', '%'.$search.'%');
        }

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

        $countries->each(function ($country) use ($totalPosts) {
            $country->post_percentage = $totalPosts > 0
                ? round(($country->posts_count / $totalPosts) * 100, 1)
                : 0;

            $country->top_user = $country->users->first();

            $country->top_user_posts = $country->top_user
                ? $country->top_user->posts_count
                : 0;
        });

        $avgPostsPerUser = $totalUsers > 0
            ? round($totalPosts / $totalUsers, 2)
            : 0;

        $chartLabels = [];
        $chartData = [];

        foreach ($countries as $country) {
            $chartLabels[] = $country->name;
            $chartData[] = $country->posts_count;
        }

        $pieLabels = [];
        $pieData = [];

        foreach ($countries as $country) {
            $pieLabels[] = $country->name;
            $pieData[] = $country->posts_count;
        }

        $topContributors = User::withCount('posts')
            ->with('country')
            ->orderByDesc('posts_count')
            ->limit(10)
            ->get();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();
        $previousYearStart = Carbon::now()->subYear()->startOfYear();
        $previousYearEnd = Carbon::now()->subYear()->endOfYear();

        $currentYearPosts = Post::whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count();
        $previousYearPosts = Post::whereBetween('created_at', [$previousYearStart, $previousYearEnd])->count();

        $yoyGrowth = 0;
        if ($previousYearPosts > 0) {
            $yoyGrowth = round(($currentYearPosts - $previousYearPosts) / $previousYearPosts * 100, 1);
        } elseif ($currentYearPosts > 0) {
            $yoyGrowth = 100;
        }

        return view('country-posts', compact(
            'countries',
            'totalPosts',
            'totalUsers',
            'totalCountries',
            'avgPostsPerUser',
            'chartLabels',
            'chartData',
            'pieLabels',
            'pieData',
            'topContributors',
            'yoyGrowth',
            'currentYearPosts',
            'previousYearPosts',
            'search',
            'sort'
        ));
    }

    public function countryPosts(Request $request, $countryId)
    {
        $country = Country::withCount([
            'users',
            'posts',
        ])->findOrFail($countryId);

        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $datePreset = $request->input('date_preset', 'all');
        $userId = $request->input('user_id');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $users = $country->users()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->orderBy('email')
            ->when(! empty($search), function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->get();

        $postsQuery = $country->posts()
            ->with('user')
            ->when($sortBy === 'name', function ($q) use ($sortDir) {
                $q->orderBy('posts.name', $sortDir === 'asc' ? 'asc' : 'desc');
            })
            ->when($sortBy === 'created_at', function ($q) use ($sortDir) {
                $q->orderBy('posts.created_at', $sortDir === 'asc' ? 'asc' : 'desc');
            });

        if (! empty($search)) {
            $postsQuery->where('posts.name', 'like', '%'.$search.'%');
        }

        if (! empty($userId)) {
            $postsQuery->where('posts.user_id', $userId);
        }

        if ($datePreset === '7d') {
            $postsQuery->whereBetween(
                'posts.created_at',
                [Carbon::now()->subDays(7), Carbon::now()]
            );
        } elseif ($datePreset === '30d') {
            $postsQuery->whereBetween(
                'posts.created_at',
                [Carbon::now()->subDays(30), Carbon::now()]
            );
        } elseif ($datePreset === 'this_year') {
            $postsQuery->whereBetween(
                'posts.created_at',
                [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]
            );
        }

        if (! empty($fromDate)) {
            $postsQuery->whereDate('posts.created_at', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $postsQuery->whereDate('posts.created_at', '<=', $toDate);
        }

        $posts = $postsQuery->paginate(5)->withQueryString();

        $users->each(function ($user) use ($country) {
            $user->contribution_percentage =
                $country->posts_count > 0
                    ? round(($user->posts_count / $country->posts_count) * 100, 1)
                    : 0;
        });

        $mostActiveUser = $users->first();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthPosts = $country->posts()
            ->whereBetween('posts.created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $previousMonthPosts = $country->posts()
            ->whereBetween('posts.created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        if ($previousMonthPosts > 0) {
            $growthPercentage = round(
                (($currentMonthPosts - $previousMonthPosts) / $previousMonthPosts) * 100,
                1
            );
        } elseif ($currentMonthPosts > 0) {
            $growthPercentage = 100;
        } else {
            $growthPercentage = 0;
        }

        $chartLabels = [];
        $chartData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $count = $country->posts()
                ->whereBetween(
                    'posts.created_at',
                    [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]
                )
                ->count();

            $chartLabels[] = $month->format('M Y');
            $chartData[] = $count;
        }

        return view('country-details', compact(
            'country',
            'posts',
            'users',
            'search',
            'fromDate',
            'toDate',
            'datePreset',
            'userId',
            'sortBy',
            'sortDir',
            'mostActiveUser',
            'currentMonthPosts',
            'previousMonthPosts',
            'growthPercentage',
            'chartLabels',
            'chartData'
        ));
    }

    public function exportCsv(Request $request, $countryId)
    {
        $country = Country::findOrFail($countryId);

        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $userId = $request->input('user_id');

        $postsQuery = $country->posts()->with('user')->latest();

        if (! empty($search)) {
            $postsQuery->where('posts.name', 'like', '%'.$search.'%');
        }

        if (! empty($userId)) {
            $postsQuery->where('posts.user_id', $userId);
        }

        if (! empty($fromDate)) {
            $postsQuery->whereDate('posts.created_at', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            $postsQuery->whereDate('posts.created_at', '<=', $toDate);
        }

        $posts = $postsQuery->get();

        $filename = strtolower(str_replace(' ', '-', $country->name)).'-post-report.csv';

        return response()->streamDownload(
            function () use ($posts, $country) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Post ID',
                    'Post Name',
                    'Author',
                    'Author Email',
                    'Country',
                    'Created Date',
                ]);

                foreach ($posts as $post) {
                    fputcsv($file, [
                        $post->id,
                        $post->name,
                        $post->user->name ?? 'N/A',
                        $post->user->email ?? 'N/A',
                        $country->name,
                        $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : '',
                    ]);
                }

                fclose($file);
            },
            $filename,
            ['Content-Type' => 'text/csv']
        );
    }

    public function exportPdf(Request $request, $countryId)
    {
        $country = Country::withCount(['users', 'posts'])->findOrFail($countryId);

        $posts = $country->posts()->with('user')->latest()->get();

        $users = $country->users()->withCount('posts')->orderByDesc('posts_count')->get();

        $pdf = Pdf::loadView('pdf.country-report', compact(
            'country',
            'posts',
            'users'
        ));

        return $pdf->download(strtolower(str_replace(' ', '-', $country->name)).'-post-report.pdf');
    }

    public function exportExcel(Request $request, $countryId)
    {
        $format = $request->input('format', 'xlsx');

        return Excel::download(new CountryReportExport($countryId), strtolower(str_replace(' ', '-', Country::findOrFail($countryId)->name)).'-post-report.'.$format);
    }
}
