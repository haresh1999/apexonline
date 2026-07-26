<?php

namespace App\Http\Controllers;

use App\Models\ButtonPayment;
use Illuminate\Http\Request;

class BtnTransactionController extends Controller
{
    public function index(Request $request)
    {
        $payments = ButtonPayment::when($request->filled('search'), function ($q) {
            $q->where('gateway', 'like', "%" . request('search') . "%");
        })
            ->when($request->filled('date'), function ($q) use ($request) {
                $dates = explode(' to ', $request->date);
                if (count($dates) == 2) {
                    $q->whereBetween('created_at', [
                        $dates[0] . ' 00:00:00',
                        $dates[1] . ' 23:59:59',
                    ]);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.btn_payment.index', compact('payments'));
    }
}
