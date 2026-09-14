<?php

namespace App\Exports;

use App\Models\Country;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $countryId;

    public function __construct($countryId)
    {
        $this->countryId = $countryId;
    }

    public function title(): string
    {
        return 'Users';
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Name',
            'Email',
            'Total Posts',
            'Contribution %',
        ];
    }

    public function map($user): array
    {
        $country = Country::find($this->countryId);
        $totalPosts = $country->posts_count ?? 0;

        $contribution = $totalPosts > 0
            ? round(($user->posts_count / $totalPosts) * 100, 1)
            : 0;

        return [
            $user->id,
            $user->name,
            $user->email,
            $user->posts_count,
            $contribution.'%',
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
        return Country::find($this->countryId)
            ->users()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->get();
    }
}
