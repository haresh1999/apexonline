<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TransactionController as CTransactionController;
use App\Models\ButtonPayment;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $tnxs = Transaction::with(['user' => function ($q) {
            $q->select('id', 'name');
        }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;

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
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('pg'), function ($q) use ($request) {
                $q->where('gateway', $request->pg);
            })
            ->when($request->filled('env'), function ($q) use ($request) {
                $q->where('env', $request->env);
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                if ($request->date == 'today') {
                    $q->whereDate('created_at', now()->format('Y-m-d'));
                } elseif ($request->date == 'yesterday') {
                    $q->whereDate('created_at', now()->subDay()->format('Y-m-d'));
                } elseif ($request->date == 'this-month') {
                    $q->whereBetween('created_at', [
                        now()->startOfMonth()->format('Y-m-d H:i:s'),
                        now()->endOfMonth()->format('Y-m-d H:i:s')
                    ]);
                } elseif ($request->date == 'last-month') {
                    $q->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'),
                        now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')
                    ]);
                } else {
                    $dates = explode(' to ', $request->date);
                    if (count($dates) == 2) {
                        $q->whereBetween('created_at', [
                            $dates[0] . ' 00:00:00',
                            $dates[1] . ' 23:59:59',
                        ]);
                    }
                }
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->authTnx()
            ->latest()
            ->paginate(20);

        $total = Transaction::authTnx()->count();
        $pendingTotal = Transaction::authTnx()->where('status', 'pending')->count();
        $processingTotal = Transaction::authTnx()->where('status', 'processing')->count();
        $completedTotal = Transaction::authTnx()->where('status', 'completed')->count();
        $failedTotal = Transaction::authTnx()->where('status', 'failed')->count();
        $refundedTotal = Transaction::authTnx()->where('status', 'refunded')->count();

        $users = User::pluck('name', 'id');

        $gateways = Gateway::pluck('name', 'slug');

        return view('admin.transaction.list', compact(
            'tnxs',
            'total',
            'pendingTotal',
            'processingTotal',
            'completedTotal',
            'failedTotal',
            'refundedTotal',
            'users',
            'gateways'
        ));
    }

    public function show($id)
    {
        $tnxs = Transaction::authTnx()->where('id', $id)->first();

        if (! $tnxs) {

            return redirect()->route('tnx.index');
        }

        return view('admin.transaction.show', compact('tnxs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:completed,failed,refunded,processing,pending'],
        ]);

        $tnx = Transaction::authTnx()->where('id', $id)->first();

        if (! $tnx) {

            return redirect()->back()->with('message', 'Order not found!');
        }

        $tnx->update(['status' => $request->status]);

        $callback_url = $tnx->callback_url;
        $callback_secret = auth()->user()->callback_secret;

        $sendData = [
            'transaction_id' => $tnx->id,
            'order_id' => $tnx->mr_order_id,
            'reference_id' => $tnx->reference_id,
            'amount' => $tnx->amount,
            'refund_amount' => $tnx->refund_amount,
            'status' => $tnx->status,
            'payer_name' => $tnx->payer_name,
            'payer_email' => $tnx->payer_email,
            'payer_mobile' => $tnx->payer_mobile,
            'redirect_url' => $tnx->redirect_url,
            'callback_url' => $tnx->callback_url,
        ];

        $tnxController = new CTransactionController();

        $tnxController->webhook($callback_url, $callback_secret, $sendData);

        return redirect()
            ->back()
            ->with('res.success', 'Payment updated successfully');
    }

    public function btnPayment(Request $request)
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
