<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SubscriptionPaymentExport implements FromArray, WithHeadings, WithTitle, WithColumnFormatting
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
            'BMDC',
            'Name',
            'Phone',
            'Total Paid Order',
            'Total Payment',
        ];
    }

    public function title(): string
    {
        return 'Subscription Payment';
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            // 'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

}
