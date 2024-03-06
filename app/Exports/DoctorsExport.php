<?php

namespace App\Exports;

use App\Doctors;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class DoctorsExport implements FromArray, WithHeadings
{
    use Exportable;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function array(): array
    {
        return $this->array;
    }

    public function headings(): array
    {
        return [
            'Name',
            'BMDC',
            'Phone',
            'Email',
            'Medical College',
        ];
    }

    
}
