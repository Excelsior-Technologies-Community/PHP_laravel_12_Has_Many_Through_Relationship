<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $country->name }} - Country Posts
    </title>

    <!-- Bootstrap 5.3.3 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .container-custom {
            width: 92%;
            max-width: 1200px;
            margin: 30px auto;
        }

        /* =========================
           Header
        ========================= */

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

        .back-btn {
            text-decoration: none;
            background: #555;
            color: #fff;
            padding: 10px 16px;
            border-radius: 7px;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #333;
            color: #fff;
        }

        /* =========================
           Summary Cards
        ========================= */

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.07);
        }

        .summary-card strong {
            display: block;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .summary-card span {
            color: #666;
        }

        /* =========================
           Sections
        ========================= */

        .section {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.07);
        }

        .section h2 {
            margin-top: 0;
        }

        /* =========================
           Search
        ========================= */

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
        }

        .search-btn {
            border: none;
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 7px;
            cursor: pointer;
            transition: 0.2s;
        }

        .search-btn:hover {
            background: #1d4ed8;
        }

        .clear-btn {
            text-decoration: none;
            background: #777;
            color: white;
            padding: 12px 18px;
            border-radius: 7px;
            transition: 0.2s;
        }

        .clear-btn:hover {
            background: #555;
            color: #fff;
        }

        /* =========================
           Tables
        ========================= */

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
        }

        th {
            background: #f7f7f7;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        /* =========================
           Badges
        ========================= */

        .badge-custom {
            display: inline-block;
            padding: 5px 10px;
            background: #e8f1ff;
            color: #2563eb;
            border-radius: 20px;
            font-size: 13px;
        }

        /* =========================
           Empty State
        ========================= */

        .empty {
            padding: 20px;
            text-align: center;
            color: #777;
            background: #f8f9fa;
            border-radius: 7px;
        }

        /* =========================
           Bootstrap Pagination
        ========================= */

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
        }

        /* =========================
           Responsive
        ========================= */

        @media (max-width: 700px) {

            .summary {
                grid-template-columns: 1fr;
            }

            .top {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-form {
                flex-direction: column;
            }

            .section {
                overflow-x: hidden;
            }

            .pagination-wrapper {
                flex-direction: column;
                align-items: center;
            }

            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }

        }

    </style>

</head>

<body>

<div class="container-custom">

    <!-- =========================
         Header
    ========================== -->

    <div class="top">

        <div>

            <h1>
                {{ $country->name }}
            </h1>

            <p>
                Country → Users → Posts
            </p>

        </div>

        <a
            href="{{ route('country.posts') }}"
            class="back-btn"
        >
            ← All Countries
        </a>

    </div>


    <!-- =========================
         Statistics
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
                {{ $country->users_count > 0
                    ? number_format(
                        $country->posts_count / $country->users_count,
                        1
                    )
                    : 0
                }}
            </strong>

            <span>
                Avg. Posts / User
            </span>

        </div>

    </div>


    <!-- =========================
         Country Posts
         Search + Pagination
    ========================== -->

    <div class="section">

        <h2>
            🔎 Country Posts
        </h2>

        <p>
            Posts retrieved using the
            <strong>hasManyThrough()</strong>
            relationship.
        </p>


        <!-- Search Form -->

        <form
            method="GET"
            action="{{ route('country.posts.details', $country->id) }}"
            class="search-form"
        >

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search posts by name..."
            >

            <button
                type="submit"
                class="search-btn"
            >
                Search
            </button>


            @if($search)

                <a
                    href="{{ route('country.posts.details', $country->id) }}"
                    class="clear-btn"
                >
                    Clear
                </a>

            @endif

        </form>


        <!-- Posts -->

        @if($posts->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Post Name
                            </th>

                            <th>
                                Author
                            </th>

                            <th>
                                Created
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
                                        {{ $post->user->name }}
                                    </span>

                                </td>

                                <td>
                                    {{ $post->created_at->format('d M Y') }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <!-- =========================
                 Bootstrap Pagination
            ========================== -->

            <div class="pagination-wrapper">

                <div class="pagination-info">

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

                @if($search)

                    No posts found for
                    <strong>
                        "{{ $search }}"
                    </strong>.

                @else

                    No posts available for this country.

                @endif

            </div>

        @endif

    </div>


    <!-- =========================
         Users & Their Posts
    ========================== -->

    <div class="section">

        <h2>
            👤 Users & Their Posts
        </h2>

        <p>

            Users belonging to

            <strong>
                {{ $country->name }}
            </strong>

            and their individual post counts.

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

                                        {{ $user->posts_count == 1
                                            ? 'Post'
                                            : 'Posts'
                                        }}

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">

                No users found for this country.

            </div>

        @endif

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>