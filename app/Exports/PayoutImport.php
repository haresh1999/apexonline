<?php

namespace App\Exports;

use App\Http\Controllers\Admin\PayoutController;
use App\Models\BankInfo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PayoutImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Skip first row
    }

    public function model(array $row)
    {
        if ($row[4] >= 2000) {

            $bank = BankInfo::where('account_number', $row[0])->first();

            if (!$bank) {

                $bank = BankInfo::create([
                    'account_number' => $row[0],
                    'ifsc_code' => $row[1],
                    'holder_name' => $row[2],
                    'bank_name' => $row[3],
                ]);
            }

            $payoutController = new PayoutController();

            $payoutController->requestPayout([
                'account_number' => $bank->account_number,
                'ifsc_code' => $bank->ifsc_code,
                'holder_name' => $bank->holder_name,
                'bank_name' => $bank->bank_name,
                'amount' => $row[4]
            ]);
        }
    }
}
