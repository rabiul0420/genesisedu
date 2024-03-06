<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ResultExport implements FromArray, WithHeadings
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
            'Reg NO',
            'Doctor Name',
            'Batch',
            'Discipline',
            'Obtained Mark',
            'Wrong Answer',
            'Discipline Position ',
            'Batch Position',
            'Candidate Position',
            'Overall Position',
        ];
    }

    
}
