<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CountryReportExport implements Export, WithMultipleSheets
{
    protected $countryId;

    public function __construct($countryId)
    {
        $this->countryId = $countryId;
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->countryId),
            new UsersSheet($this->countryId),
            new PostsSheet($this->countryId),
        ];
    }
}
