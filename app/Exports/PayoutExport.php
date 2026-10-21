<?php

namespace App\Exports;

use App\Models\BankInfo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PayoutExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize
{
    public function collection()
    {
        return BankInfo::all();
    }

    public function headings(): array
    {
        return [
            'ACCOUNT_NUMBER',
            'IFSC_CODE',
            'HOLDER_NAME',
            'BANK_NAME',
            'AMOUNT',
        ];
    }

    public function map($data): array
    {
        return [
            $data->account_number,
            $data->ifsc_code,
            $data->holder_name,
            $data->bank_name,
            '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Bold and center the header
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Header row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Center all cells horizontally and vertically
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        // Preserve account numbers and IFSC codes as text
        $sheet->getStyle("A2:B{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);

        // Freeze header row
        $sheet->freezePane('A2');

        // Enable filters
        $sheet->setAutoFilter("A1:{$highestColumn}{$highestRow}");

        return [];
    }
}
