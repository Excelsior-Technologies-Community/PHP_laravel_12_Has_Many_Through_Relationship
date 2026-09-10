<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Has Many Through - Country Dashboard
    </title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .dashboard-container {
            width: 92%;
            max-width: 1250px;
            margin: 35px auto;
        }

        /* =========================
           Header
        ========================= */

        .dashboard-header {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 30px;
        }

        .dashboard-header p {
            margin: 8px 0 0;
            color: #666;
        }

        .relationship {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background: #e8f1ff;
            color: #2563eb;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        /* =========================
           Summary Cards
        ========================= */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
            text-align: center;
        }

        .summary-card .icon {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .summary-card .number {
            font-size: 30px;
            font-weight: bold;
            display: block;
        }

        .summary-card .label {
            color: #666;
            margin-top: 5px;
            display: block;
        }

        /* =========================
           Main Section
        ========================= */

        .section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
        }

        .section-header {
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 23px;
        }

        .section-header p {
            color: #666;
            margin-top: 8px;
        }

        /* =========================
           Country Table
        ========================= */

        .table-responsive {
            border-radius: 8px;
        }

        table {
            vertical-align: middle !important;
        }

        th {
            white-space: nowrap;
        }

        td {
            vertical-align: middle;
        }

        .country-name {
            font-weight: bold;
            font-size: 16px;
        }

        .count-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e8f1ff;
            color: #2563eb;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .post-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e9f8ef;
            color: #198754;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        /* =========================
           Progress Bar
        ========================= */

        .percentage-wrapper {
            min-width: 160px;
        }

        .percentage-text {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .progress {
            height: 8px;
            border-radius: 10px;
        }

        /* =========================
           Top Contributor
        ========================= */

        .top-user {
            font-weight: bold;
        }

        .top-user-posts {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .trophy {
            font-size: 18px;
            margin-right: 5px;
        }

        /* =========================
           View Button
        ========================= */

        .view-btn {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 7px;
            background: #2563eb;
            color: #fff;
            font-size: 13px;
            display: inline-block;
        }

        .view-btn:hover {
            background: #1d4ed8;
            color: #fff;
        }

        /* =========================
           Empty State
        ========================= */

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        /* =========================
           Responsive
        ========================= */

        @media (max-width: 900px) {

            .summary-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .dashboard-container {
                width: 95%;
                margin: 20px auto;
            }

            .dashboard-header h1 {
                font-size: 24px;
            }

            .section {
                padding: 18px;
            }

        }

    </style>

</head>

<body>

<div class="dashboard-container">

    <!-- =========================
         Dashboard Header
    ========================== -->

    <div class="dashboard-header">

        <h1>
            🌍 Has Many Through Relationship
        </h1>

        <p>
            Country-wise post reporting and relationship analytics
        </p>

        <div class="relationship">
            Country → Users → Posts
        </div>

    </div>


    <!-- =========================
         Summary Statistics
    ========================== -->

    <div class="summary-grid">

        <div class="summary-card">

            <div class="icon">
                🌍
            </div>

            <span class="number">
                {{ $countries->count() }}
            </span>

            <span class="label">
                Total Countries
            </span>

        </div>


        <div class="summary-card">

            <div class="icon">
                👥
            </div>

            <span class="number">
                {{ $countries->sum('users_count') }}
            </span>

            <span class="label">
                Total Users
            </span>

        </div>


        <div class="summary-card">

            <div class="icon">
                📝
            </div>

            <span class="number">
                {{ $totalPosts }}
            </span>

            <span class="label">
                Total Posts
            </span>

        </div>

    </div>


    <!-- =========================
         Country Analytics
    ========================== -->

    <div class="section">

        <div class="section-header">

            <h2>
                📊 Country Post Analytics
            </h2>

            <p>
                Countries ranked by total posts using the
                <strong>hasManyThrough()</strong> relationship.
            </p>

        </div>


        @if($countries->count())

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead class="table-light">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Country
                            </th>

                            <th>
                                Users
                            </th>

                            <th>
                                Total Posts
                            </th>

                            <th>
                                Post Share
                            </th>

                            <th>
                                🏆 Top Contributor
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($countries as $country)

                            <tr>

                                <!-- Rank -->

                                <td>

                                    <strong>
                                        {{ $loop->iteration }}
                                    </strong>

                                </td>


                                <!-- Country -->

                                <td>

                                    <span class="country-name">

                                        {{ $country->name }}

                                    </span>

                                </td>


                                <!-- Users -->

                                <td>

                                    <span class="count-badge">

                                        {{ $country->users_count }}

                                        {{ $country->users_count == 1
                                            ? 'User'
                                            : 'Users'
                                        }}

                                    </span>

                                </td>


                                <!-- Posts -->

                                <td>

                                    <span class="post-badge">

                                        {{ $country->posts_count }}

                                        {{ $country->posts_count == 1
                                            ? 'Post'
                                            : 'Posts'
                                        }}

                                    </span>

                                </td>


                                <!-- Percentage -->

                                <td>

                                    <div class="percentage-wrapper">

                                        <div class="percentage-text">

                                            {{ $country->post_percentage }}%

                                        </div>

                                        <div class="progress">

                                            <div
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width: {{ $country->post_percentage }}%;"
                                                aria-valuenow="{{ $country->post_percentage }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            >
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- Top Contributor -->

                                <td>

                                    @if($country->top_user)

                                        <div class="top-user">

                                            <span class="trophy">
                                                🏆
                                            </span>

                                            {{ $country->top_user->name }}

                                        </div>

                                        <div class="top-user-posts">

                                            {{ $country->top_user_posts }}

                                            {{ $country->top_user_posts == 1
                                                ? 'Post'
                                                : 'Posts'
                                            }}

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            No users
                                        </span>

                                    @endif

                                </td>


                                <!-- Action -->

                                <td>

                                    <a
                                        href="{{ route('country.posts.details', $country->id) }}"
                                        class="view-btn"
                                    >
                                        View Posts
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">

                No countries found.

            </div>

        @endif

    </div>


    <!-- =========================
         Analytics Explanation
    ========================== -->

    <div class="section">

        <div class="section-header">

            <h2>
                💡 Relationship Analytics
            </h2>

            <p>
                This dashboard demonstrates how Laravel's
                <strong>hasManyThrough()</strong> relationship can
                be used for reporting and analytics.
            </p>

        </div>

        <div class="row g-3">

            <div class="col-md-4">

                <div class="alert alert-primary mb-0">

                    <strong>
                        Country → Posts
                    </strong>

                    <br>

                    Posts are retrieved through users using
                    <code>hasManyThrough()</code>.

                </div>

            </div>


            <div class="col-md-4">

                <div class="alert alert-success mb-0">

                    <strong>
                        Post Distribution
                    </strong>

                    <br>

                    Each country's percentage of total posts
                    is calculated automatically.

                </div>

            </div>


            <div class="col-md-4">

                <div class="alert alert-warning mb-0">

                    <strong>
                        Top Contributors
                    </strong>

                    <br>

                    Users are ranked according to their
                    individual post counts.

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>