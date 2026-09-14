<?php

namespace App\Exports;

use App\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $countryId;

    public function __construct($countryId)
    {
        $this->countryId = $countryId;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function collection(): Enumerable
    {
        $country = Country::withCount(['users', 'posts'])->find($this->countryId);

        if (! $country) {
            return new Collection([]);
        }

        $totalUsers = $country->users_count;
        $totalPosts = $country->posts_count;
        $avgPostsPerUser = $totalUsers > 0
            ? round($totalPosts / $totalUsers, 2)
            : 0;

        $topUser = $country->users()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->first();

        return new Collection([
            ['Country Name', $country->name],
            ['Total Users', $totalUsers],
            ['Total Posts', $totalPosts],
            ['Average Posts Per User', $avgPostsPerUser],
            ['Top Contributor', $topUser ? $topUser->name : 'N/A'],
            ['Top Contributor Posts', $topUser ? $topUser->posts_count : 0],
            ['Report Generated At', now()->format('Y-m-d H:i:s')],
        ]);
    }
}
