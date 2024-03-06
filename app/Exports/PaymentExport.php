<?php

namespace App\Exports;

use App\PaymentInfo;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class PaymentExport implements FromArray, WithHeadings
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
            'Id',
            'Name',
            'Batch',
            'Reg No',
            'Mobile Number',
            'Trans_Id',
            'Paid Amount',
            'Date',
        ];
    }

    
}
