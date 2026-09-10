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

        .dashboard-header {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,.07);
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
            box-shadow: 0 3px 12px rgba(0,0,0,.07);
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

        .section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.07);
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

        .filter-box {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 20px;
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

        .top-user {
            font-weight: bold;
        }

        .top-user-posts {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

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

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

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

    <!-- HEADER -->

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


    <!-- SUMMARY -->

    <div class="summary-grid">

        <div class="summary-card">

            <div class="icon">🌍</div>

            <span class="number">
                {{ $countries->count() }}
            </span>

            <span class="label">
                Total Countries
            </span>

        </div>


        <div class="summary-card">

            <div class="icon">👥</div>

            <span class="number">
                {{ $countries->sum('users_count') }}
            </span>

            <span class="label">
                Total Users
            </span>

        </div>


        <div class="summary-card">

            <div class="icon">📝</div>

            <span class="number">
                {{ $totalPosts }}
            </span>

            <span class="label">
                Total Posts
            </span>

        </div>

    </div>


    <!-- SEARCH + RANKING -->

    <div class="section">

        <div class="section-header">

            <h2>
                🔎 Search & 🏆 Country Ranking
            </h2>

            <p>
                Search countries and rank them by users or posts.
            </p>

        </div>


        <div class="filter-box">

            <form
                method="GET"
                action="{{ route('country.posts') }}"
            >

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search Country
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Search country..."
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Rank By
                        </label>

                        <select
                            name="sort"
                            class="form-select"
                        >

                            <option
                                value="posts"
                                {{ $sort === 'posts' ? 'selected' : '' }}
                            >
                                📝 Total Posts
                            </option>

                            <option
                                value="users"
                                {{ $sort === 'users' ? 'selected' : '' }}
                            >
                                👥 Total Users
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>


        @if($countries->count())

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Rank
                            </th>

                            <th>
                                Country
                            </th>

                            <th>
                                Users
                            </th>

                            <th>
                                Posts
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

                            <td>
                                <strong>
                                    #{{ $loop->iteration }}
                                </strong>
                            </td>


                            <td>

                                <span class="country-name">
                                    {{ $country->name }}
                                </span>

                            </td>


                            <td>

                                <span class="count-badge">

                                    {{ $country->users_count }}

                                    {{ $country->users_count == 1
                                        ? 'User'
                                        : 'Users'
                                    }}

                                </span>

                            </td>


                            <td>

                                <span class="post-badge">

                                    {{ $country->posts_count }}

                                    {{ $country->posts_count == 1
                                        ? 'Post'
                                        : 'Posts'
                                    }}

                                </span>

                            </td>


                            <td>

                                <div class="percentage-wrapper">

                                    <div class="percentage-text">

                                        {{ $country->post_percentage }}%

                                    </div>

                                    <div class="progress">

                                        <div
                                            class="progress-bar"
                                            style="width: {{ min($country->post_percentage, 100) }}%;"
                                        ></div>

                                    </div>

                                </div>

                            </td>


                            <td>

                                @if($country->top_user)

                                    <strong>
                                        🏆 {{ $country->top_user->name }}
                                    </strong>

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


                            <td>

                                <a
                                    href="{{ route(
                                        'country.posts.details',
                                        $country->id
                                    ) }}"
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


    <!-- EXPLANATION -->

    <div class="section">

        <h2>
            💡 Analytics
        </h2>

        <div class="row g-3 mt-2">

            <div class="col-md-4">

                <div class="alert alert-primary">

                    <strong>
                        Country → Posts
                    </strong>

                    <br>

                    Posts are retrieved through
                    <code>hasManyThrough()</code>.

                </div>

            </div>


            <div class="col-md-4">

                <div class="alert alert-success">

                    <strong>
                        Country Ranking
                    </strong>

                    <br>

                    Countries can be ranked by users or posts.

                </div>

            </div>


            <div class="col-md-4">

                <div class="alert alert-warning">

                    <strong>
                        Search
                    </strong>

                    <br>

                    Quickly search countries from the dashboard.

                </div>

            </div>

        </div>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>