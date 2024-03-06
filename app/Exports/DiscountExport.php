<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DiscountExport implements FromArray, WithHeadings, WithTitle, WithColumnFormatting
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
            'Doctor Name',
            'BMDC No',
            'Discount For',
            'Batch Name',
            'Discount Code',
            'Amount',
            'Code Duration',
            'Used',
            'Reference',
            'Created By',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Discount';
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            // 'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

}
