<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $country->name }} - Country Report
    </title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Chart.js -->

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    <style>

        body {
            margin: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container-custom {
            width: 92%;
            max-width: 1250px;
            margin: 30px auto;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .top h1 {
            margin: 0;
        }

        .top p {
            margin-top: 8px;
            color: #666;
        }

        .btn-custom {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            border-radius: 7px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .back-btn {
            background: #555;
        }

        .export-btn {
            background: #198754;
        }

        .print-btn {
            background: #6f42c1;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,.07);
        }

        .summary-card strong {
            display: block;
            font-size: 27px;
            margin-bottom: 5px;
        }

        .summary-card span {
            color: #666;
        }

        .section {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,.07);
        }

        .section h2 {
            margin-top: 0;
        }

        .filter-box {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            text-align: left;
            white-space: nowrap;
            vertical-align: middle;
        }

        th {
            background: #f7f7f7;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        .badge-custom {
            display: inline-block;
            padding: 5px 10px;
            background: #e8f1ff;
            color: #2563eb;
            border-radius: 20px;
            font-size: 13px;
        }

        .contribution-bar {
            min-width: 150px;
        }

        .small-text {
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
        }

        .growth-up {
            color: #198754;
            font-weight: bold;
        }

        .growth-down {
            color: #dc3545;
            font-weight: bold;
        }

        .most-active {
            background: #fff8e1;
            border-left: 5px solid #ffc107;
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        .empty {
            padding: 20px;
            text-align: center;
            color: #777;
            background: #f8f9fa;
            border-radius: 7px;
        }

        .post-view-btn {
            border: none;
            background: #2563eb;
            color: white;
            padding: 7px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .post-view-btn:hover {
            background: #1d4ed8;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        @media (max-width: 1000px) {

            .summary {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 700px) {

            .summary {
                grid-template-columns: 1fr;
            }

            .top {
                flex-direction: column;
                align-items: flex-start;
            }

        }


        /* =========================
           PRINT
        ========================= */

        @media print {

            body {
                background: white;
            }

            .no-print,
            .filter-box,
            .pagination-wrapper,
            button,
            a {
                display: none !important;
            }

            .container-custom {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .section,
            .summary-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .chart-container {
                height: 300px;
            }

        }

    </style>

</head>


<body>

<div class="container-custom">


    <!-- =========================
         HEADER
    ========================== -->

    <div class="top no-print">

        <div>

            <h1>
                🌍 {{ $country->name }}
            </h1>

            <p>
                Country → Users → Posts
            </p>

        </div>


        <div class="d-flex gap-2 flex-wrap">

            <a
                href="{{ route('country.posts') }}"
                class="btn-custom back-btn"
            >
                ← All Countries
            </a>


            <button
                onclick="window.print()"
                class="btn-custom print-btn no-print"
            >
                🖨️ Print Report
            </button>

            <div class="dropdown no-print">
                <button class="btn-custom export-btn dropdown-toggle"
                    type="button" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    📥 Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li>
                        <a href="{{ route('country.posts.export.csv', $country->id) . '?' . http_build_query([
                            'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate,
                            'user_id' => $userId, 'date_preset' => $datePreset,
                            'sort_by' => $sortBy, 'sort_dir' => $sortDir,
                        ]) }}"
                            class="dropdown-item">
                            📄 CSV
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('country.posts.export.pdf', $country->id) . '?' . http_build_query([
                            'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate,
                            'user_id' => $userId, 'date_preset' => $datePreset,
                            'sort_by' => $sortBy, 'sort_dir' => $sortDir,
                        ]) }}"
                            class="dropdown-item">
                            📄 PDF
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('country.posts.export.excel', $country->id) . '?' . http_build_query([
                            'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate,
                            'user_id' => $userId, 'date_preset' => $datePreset,
                            'sort_by' => $sortBy, 'sort_dir' => $sortDir,
                            'format' => 'xlsx',
                        ]) }}"
                            class="dropdown-item">
                            📊 Excel (XLSX)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('country.posts.export.excel', $country->id) . '?' . http_build_query([
                            'search' => $search, 'from_date' => $fromDate, 'to_date' => $toDate,
                            'user_id' => $userId, 'date_preset' => $datePreset,
                            'sort_by' => $sortBy, 'sort_dir' => $sortDir,
                            'format' => 'csv',
                        ]) }}"
                            class="dropdown-item">
                            📊 Excel (CSV)
                        </a>
                    </li>
                </ul>
            </div>

        </div>

    </div>


    <!-- PRINT TITLE -->

    <div class="d-none d-print-block mb-4">

        <h1>
            {{ $country->name }} - Country Post Report
        </h1>

        <p>
            Generated on {{ now()->format('d M Y H:i') }}
        </p>

    </div>


    <!-- =========================
         SUMMARY
    ========================== -->

    <div class="summary">


        <div class="summary-card">

            <strong>
                {{ $country->users_count }}
            </strong>

            <span>
                Total Users
            </span>

        </div>


        <div class="summary-card">

            <strong>
                {{ $country->posts_count }}
            </strong>

            <span>
                Total Posts
            </span>

        </div>


        <div class="summary-card">

            <strong>
                {{ $currentMonthPosts }}
            </strong>

            <span>
                Current Month Posts
            </span>

        </div>


        <div class="summary-card">

            <strong
                class="{{ $growthPercentage >= 0
                    ? 'text-success'
                    : 'text-danger'
                }}"
            >
                {{ $growthPercentage > 0 ? '+' : '' }}
                {{ $growthPercentage }}%
            </strong>

            <span>
                Monthly Growth
            </span>

        </div>


    </div>


    <!-- =========================
         SEARCH & FILTER
     ========================== -->

    <div class="section no-print">

        <h2>
            🔎 Search & 📅 Date Filter
        </h2>

        <p>
            Filter posts by name, author email, user, date range, or sort.
        </p>


        <div class="filter-box">

            <form
                method="GET"
                action="{{ route(
                    'country.posts.details',
                    $country->id
                ) }}"
            >

                <div class="row g-3">

                    <!-- Search (post name + author email) -->

                    <div class="col-md-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Post name or author email..."
                        >

                    </div>

                    <!-- User Filter -->

                    <div class="col-md-2">

                        <label class="form-label">
                            Filter by User
                        </label>

                        <select
                            name="user_id"
                            class="form-select"
                        >

                            <option value="">
                                All Users
                            </option>

                            @foreach($users as $u)

                                <option
                                    value="{{ $u->id }}"
                                    {{ ($userId ?? '') == $u->id ? 'selected' : '' }}
                                >
                                    {{ $u->name }}
                                    ({{ $u->posts_count }})
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <!-- Date Presets -->

                    <div class="col-md-2">

                        <label class="form-label">
                            Date Preset
                        </label>

                        <select
                            name="date_preset"
                            class="form-select"
                        >

                            <option
                                value="all"
                                {{ ($datePreset ?? 'all') === 'all' ? 'selected' : '' }}
                            >
                                All Time
                            </option>

                            <option
                                value="7d"
                                {{ ($datePreset ?? 'all') === '7d' ? 'selected' : '' }}
                            >
                                Last 7 Days
                            </option>

                            <option
                                value="30d"
                                {{ ($datePreset ?? 'all') === '30d' ? 'selected' : '' }}
                            >
                                Last 30 Days
                            </option>

                            <option
                                value="this_year"
                                {{ ($datePreset ?? 'all') === 'this_year' ? 'selected' : '' }}
                            >
                                This Year
                            </option>

                        </select>

                    </div>

                    <!-- From / To Date -->

                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from_date"
                            value="{{ $fromDate }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to_date"
                            value="{{ $toDate }}"
                            class="form-control"
                        >

                    </div>

                    <!-- Sort Options -->

                    <div class="col-md-1">

                        <label class="form-label">
                            Sort By
                        </label>

                        <select
                            name="sort_by"
                            class="form-select"
                        >

                            <option
                                value="created_at"
                                {{ ($sortBy ?? 'created_at') === 'created_at' ? 'selected' : '' }}
                            >
                                Date
                            </option>

                            <option
                                value="name"
                                {{ ($sortBy ?? 'created_at') === 'name' ? 'selected' : '' }}
                            >
                                Name
                            </option>

                        </select>

                    </div>

                    <div class="col-md-1">

                        <label class="form-label">
                            Order
                        </label>

                        <select
                            name="sort_dir"
                            class="form-select"
                        >

                            <option
                                value="desc"
                                {{ ($sortDir ?? 'desc') === 'desc' ? 'selected' : '' }}
                            >
                                Desc
                            </option>

                            <option
                                value="asc"
                                {{ ($sortDir ?? 'desc') === 'asc' ? 'selected' : '' }}
                            >
                                Asc
                            </option>

                        </select>

                    </div>

                    <!-- Filter Button -->

                    <div class="col-md-12 d-flex align-items-end justify-content-end">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Apply Filters
                            </button>

                            @if($search || $fromDate || $toDate || $datePreset !== 'all' || $userId || $sortBy !== 'created_at' || $sortDir !== 'desc')

                                <a
                                    href="{{ route(
                                        'country.posts.details',
                                        $country->id
                                    ) }}"
                                    class="btn btn-secondary"
                                >
                                    Clear Filters
                                </a>

                            @endif

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =========================
         GROWTH ANALYTICS
    ========================== -->

    <div class="section">

        <h2>
            📈 Growth Analytics
        </h2>

        <div class="row g-3 mt-2">


            <div class="col-md-4">

                <div class="card border-0 bg-light p-3">

                    <div class="small-text">
                        Current Month
                    </div>

                    <div class="stat-number">
                        {{ $currentMonthPosts }}
                    </div>

                    <div>
                        Posts
                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-0 bg-light p-3">

                    <div class="small-text">
                        Previous Month
                    </div>

                    <div class="stat-number">
                        {{ $previousMonthPosts }}
                    </div>

                    <div>
                        Posts
                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-0 p-3
                    {{ $growthPercentage >= 0
                        ? 'bg-success-subtle'
                        : 'bg-danger-subtle'
                    }}"
                >

                    <div class="small-text">
                        Growth
                    </div>

                    <div
                        class="stat-number
                        {{ $growthPercentage >= 0
                            ? 'text-success'
                            : 'text-danger'
                        }}"
                    >

                        {{ $growthPercentage > 0 ? '+' : '' }}
                        {{ $growthPercentage }}%

                    </div>

                    <div>

                        {{ $growthPercentage >= 0
                            ? 'Growth'
                            : 'Decline'
                        }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
         MOST ACTIVE USER
    ========================== -->

    <div class="section most-active">

        <h2>
            🔥 Most Active User
        </h2>


        @if($mostActiveUser)

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h3>
                        🏆 {{ $mostActiveUser->name }}
                    </h3>

                    <p class="mb-1">

                        {{ $mostActiveUser->email }}

                    </p>

                    <strong>

                        {{ $mostActiveUser->posts_count }}

                        {{ $mostActiveUser->posts_count == 1
                            ? 'Post'
                            : 'Posts'
                        }}

                    </strong>

                </div>


                <div class="col-md-4 text-md-end">

                    <div class="stat-number">

                        {{ $mostActiveUser->contribution_percentage }}%

                    </div>

                    <div>
                        Contribution
                    </div>

                </div>

            </div>

        @else

            <p class="text-muted mb-0">
                No active user found.
            </p>

        @endif

    </div>


    <!-- =========================
         MONTHLY CHART
    ========================== -->

    <div class="section">

        <h2>
            📊 Monthly Post Activity
        </h2>

        <p>
            Post activity for the last 12 months.
        </p>


        <div class="chart-container">

            <canvas id="postActivityChart"></canvas>

        </div>

    </div>


    <!-- =========================
         POSTS
    ========================== -->

    <div class="section">

        <h2>
            📝 Country Posts
        </h2>

        <p>
            Posts retrieved using
            <strong>hasManyThrough()</strong>.
        </p>


        @if($posts->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Post
                            </th>

                            <th>
                                Author
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Created
                            </th>

                            <th class="no-print">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach($posts as $post)

                        <tr>

                            <td>
                                {{ $posts->firstItem() + $loop->index }}
                            </td>


                            <td>

                                <strong>
                                    {{ $post->name }}
                                </strong>

                            </td>


                            <td>

                                <span class="badge-custom">

                                    {{ $post->user->name ?? 'N/A' }}

                                </span>

                            </td>


                            <td>

                                <span class="badge-custom">

                                    {{ $post->user->email ?? 'N/A' }}

                                </span>

                            </td>


                            <td>

                                {{ $post->created_at
                                    ? $post->created_at->format('d M Y')
                                    : 'N/A'
                                }}

                            </td>


                            <td class="no-print">

                                <button
                                    type="button"
                                    class="post-view-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#postModal"
                                    data-id="{{ $post->id }}"
                                    data-name="{{ $post->name }}"
                                    data-author="{{ $post->user->name ?? 'N/A' }}"
                                    data-email="{{ $post->user->email ?? 'N/A' }}"
                                    data-country="{{ $country->name }}"
                                    data-date="{{ $post->created_at
                                        ? $post->created_at->format('d M Y H:i')
                                        : 'N/A'
                                    }}"
                                >
                                    📋 Details
                                </button>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>


            <!-- PAGINATION -->

            <div class="pagination-wrapper no-print">

                <div>

                    Showing

                    <strong>
                        {{ $posts->firstItem() ?? 0 }}
                    </strong>

                    to

                    <strong>
                        {{ $posts->lastItem() ?? 0 }}
                    </strong>

                    of

                    <strong>
                        {{ $posts->total() }}
                    </strong>

                    posts

                </div>


                <div>

                    {{ $posts->onEachSide(1)->links() }}

                </div>

            </div>

        @else

            <div class="empty">

                @if($search || $fromDate || $toDate)

                    No posts found for the selected filters.

                @else

                    No posts available for this country.

                @endif

            </div>

        @endif

    </div>


    <!-- =========================
         USER CONTRIBUTIONS
    ========================== -->

    <div class="section">

        <h2>
            👤 User Contributions
        </h2>

        <p>
            Each user's percentage of this country's total posts.
        </p>


        @if($users->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                User
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Posts
                            </th>

                            <th>
                                Contribution %
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach($users as $user)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>


                            <td>
                                <strong>
                                    {{ $user->name }}
                                </strong>
                            </td>


                            <td>
                                {{ $user->email }}
                            </td>


                            <td>

                                <span class="badge-custom">

                                    {{ $user->posts_count }}

                                </span>

                            </td>


                            <td>

                                <div class="contribution-bar">

                                    <div class="small-text">

                                        {{ $user->contribution_percentage }}%

                                    </div>

                                    <div class="progress">

                                        <div
                                            class="progress-bar"
                                            style="width: {{ min($user->contribution_percentage, 100) }}%;"
                                        ></div>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">
                No users found.
            </div>

        @endif

    </div>

</div>


<!-- =========================
     POST DETAILS MODAL
========================== -->

<div
    class="modal fade"
    id="postModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">
                    📋 Post Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">


                <div class="mb-3">

                    <label class="fw-bold">
                        Post ID
                    </label>

                    <div id="modalPostId">
                        -
                    </div>

                </div>


                <div class="mb-3">

                    <label class="fw-bold">
                        Post Name
                    </label>

                    <div id="modalPostName">
                        -
                    </div>

                </div>


                <div class="mb-3">

                    <label class="fw-bold">
                        Author
                    </label>

                    <div id="modalAuthor">
                        -
                    </div>

                </div>


                <div class="mb-3">

                    <label class="fw-bold">
                        Author Email
                    </label>

                    <div id="modalEmail">
                        -
                    </div>

                </div>


                <div class="mb-3">

                    <label class="fw-bold">
                        Country
                    </label>

                    <div id="modalCountry">
                        -
                    </div>

                </div>


                <div class="mb-3">

                    <label class="fw-bold">
                        Created Date
                    </label>

                    <div id="modalDate">
                        -
                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>


<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

/*
|--------------------------------------------------------------------------
| Monthly Post Activity Chart
|--------------------------------------------------------------------------
*/

const chartLabels = @json($chartLabels);

const chartData = @json($chartData);

const chartElement =
    document.getElementById('postActivityChart');


new Chart(chartElement, {

    type: 'line',

    data: {

        labels: chartLabels,

        datasets: [

            {
                label: 'Posts',

                data: chartData,

                borderWidth: 3,

                tension: 0.3,

                fill: true
            }

        ]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {
                display: true
            }

        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {
                    precision: 0
                }

            }

        }

    }

});


/*
|--------------------------------------------------------------------------
| Post Details Modal
|--------------------------------------------------------------------------
*/

const postModal =
    document.getElementById('postModal');


postModal.addEventListener(
    'show.bs.modal',
    function (event) {

        const button = event.relatedTarget;


        document.getElementById('modalPostId')
            .textContent =
            button.getAttribute('data-id');


        document.getElementById('modalPostName')
            .textContent =
            button.getAttribute('data-name');


        document.getElementById('modalAuthor')
            .textContent =
            button.getAttribute('data-author');


        document.getElementById('modalEmail')
            .textContent =
            button.getAttribute('data-email');


        document.getElementById('modalCountry')
            .textContent =
            button.getAttribute('data-country');


        document.getElementById('modalDate')
            .textContent =
            button.getAttribute('data-date');

    }
);

</script>

</body>

</html>