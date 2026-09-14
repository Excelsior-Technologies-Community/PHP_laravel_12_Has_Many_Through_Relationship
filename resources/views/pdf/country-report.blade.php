<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $country->name }} - Country Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1 { font-size: 24px; margin-bottom: 5px; color: #1a1a2e; }
        h2 { font-size: 18px; margin-top: 25px; margin-bottom: 10px; color: #2563eb; border-bottom: 2px solid #e8f1ff; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; font-size: 12px; }
        th { background-color: #f4f6f9; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .summary-box { background: #f4f6f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-label { font-size: 11px; color: #666; display: block; }
        .summary-value { font-size: 20px; font-weight: bold; color: #1a1a2e; }
        .header { margin-bottom: 20px; }
        .generated { font-size: 11px; color: #999; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $country->name }} - Post Report</h1>
        <p>Country → Users → Posts</p>
        <div class="generated">Generated on {{ now()->format('d M Y H:i') }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total Users</span>
            <span class="summary-value">{{ $country->users_count }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Posts</span>
            <span class="summary-value">{{ $country->posts_count }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Avg Posts/User</span>
            <span class="summary-value">{{ $country->users_count > 0 ? round($country->posts_count / $country->users_count, 1) : 0 }}</span>
        </div>
    </div>

    <h2>Posts</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Post Name</th>
                <th>Author</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->name }}</td>
                    <td>{{ $post->user->name ?? 'N/A' }}</td>
                    <td>{{ $post->user->email ?? 'N/A' }}</td>
                    <td>{{ $post->created_at ? $post->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No posts found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>User Contributions</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Email</th>
                <th>Posts</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->posts_count }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
