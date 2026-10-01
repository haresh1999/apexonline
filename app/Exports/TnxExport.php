<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TnxExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Transaction::with(['user' => function ($q) {
            $q->select('id', 'name');
        }])
            ->when(request()->filled('search'), function ($q) {
                $search = request('search');

                $q->where(function ($q) use ($search) {
                    $q->where('payer_name', 'like', "%{$search}%")
                        ->orWhere('payer_email', 'like', "%{$search}%")
                        ->orWhere('payer_mobile', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhere('redirect_url', 'like', "%{$search}%")
                        ->orWhere('callback_url', 'like', "%{$search}%")
                        ->orWhere('reference_id', 'like', "%{$search}%")
                        ->orWhere('mr_order_id', 'like', "%{$search}%");
                });
            })
            ->when(request()->filled('status'), function ($q) {
                $q->where('status', request('status'));
            })
            ->when(request()->filled('pg'), function ($q) {
                $q->where('gateway', request('pg'));
            })
            ->when(request()->filled('date'), function ($q) {
                if (request('date') == 'today') {
                    $q->whereDate('created_at', now()->format('Y-m-d'));
                } elseif (request('date') == 'yesterday') {
                    $q->whereDate('created_at', now()->subDay()->format('Y-m-d'));
                } elseif (request('date') == 'this-month') {
                    $q->whereBetween('created_at', [
                        now()->startOfMonth()->format('Y-m-d H:i:s'),
                        now()->endOfMonth()->format('Y-m-d H:i:s')
                    ]);
                } elseif (request('date') == 'last-month') {
                    $q->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'),
                        now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')
                    ]);
                } else {
                    $dates = explode(' to ', request('date'));
                    if (count($dates) == 2) {
                        $q->whereBetween('created_at', [
                            $dates[0] . ' 00:00:00',
                            $dates[1] . ' 23:59:59',
                        ]);
                    } else {
                        $q->whereDate('created_at', request('date'));
                    }
                }
            })
            ->when(request()->filled('user_id'), function ($q) {
                $q->where('user_id', request('user_id'));
            })
            ->where('env', 'production')
            ->authTnx()
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ORDER ID',
            'MERCHANT ORDER ID',
            'COMPANY',
            'PAYER NAME',
            'PAYER EMAIL',
            'PAYER MOBILE',
            'STATUS',
            'GATEWAY',
            'AMOUNT',
            'REFERENCE ID',
            'PAYMENT ID',
            'DATE'
        ];
    }

    public function map($data): array
    {
        return [
            $data->order_id,
            $data->mr_order_id,
            $data->user->name,
            $data->payer_name,
            $data->payer_email,
            $data->payer_mobile,
            $data->status,
            $data->gateway,
            $data->amount,
            $data->reference_id,
            $data->payment_id,
            Carbon::parse($data->created_at)->format('d-m-Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Header bold + center
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Header height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Center all cells
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        // Freeze header
        $sheet->freezePane('A2');

        // Filter
        $sheet->setAutoFilter("A1:{$highestColumn}{$highestRow}");

        return [];
    }
}
