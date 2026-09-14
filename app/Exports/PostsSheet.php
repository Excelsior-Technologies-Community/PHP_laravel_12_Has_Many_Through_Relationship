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

class PostsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $countryId;

    public function __construct($countryId)
    {
        $this->countryId = $countryId;
    }

    public function title(): string
    {
        return 'Posts';
    }

    public function headings(): array
    {
        return [
            'Post ID',
            'Post Name',
            'Author',
            'Author Email',
            'Country',
            'Created At',
        ];
    }

    public function map($post): array
    {
        return [
            $post->id,
            $post->name,
            $post->user->name ?? 'N/A',
            $post->user->email ?? 'N/A',
            $post->user->country->name ?? 'N/A',
            $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : '',
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
            ->posts()
            ->with('user.country')
            ->latest()
            ->get();
    }
}
