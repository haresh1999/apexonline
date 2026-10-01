<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TransactionController as CTransactionController;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function show(string $id)
    {
        $tnxs = Transaction::authTnx()->where('id', $id)->first();

        if (! $tnxs) {

            return redirect()->route('tnx.index');
        }

        return view('admin.transaction.show', compact('tnxs'));
    }

    public function update(Request $request, string $id)
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
        $callback_secret = User::where('id', $tnx->user_id)->value('callback_secret');

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

    public function webhook()
    {
        $logs = WebhookLog::when(request()->filled('search'), function ($q) {
            $q->where(function ($q) {
                $search = request('search');
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($query) use ($search) {
                        $query->where('mr_order_id', 'like', "%{$search}%");
                    });
            });
        })
            ->when(request()->filled('date'), function ($q) {
                $dates = explode(' to ', request('date'));
                if (count($dates) == 2) {
                    $q->whereBetween('created_at', [
                        $dates[0] . ' 00:00:00',
                        $dates[1] . ' 23:59:59',
                    ]);
                }
            })
            ->authUser()
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('admin.webhook.list', compact('logs'));
    }

    public function invoice(string $id)
    {
        $tnx = Transaction::where('id', $id)->firstOrFail();

        // $cont = new CTransactionController();

        // $cont->sendCourseMail($tnx);

        $fileName = ids($tnx->id) . '.pdf';
        $path = storage_path('app/public/invoice/' . $fileName);

        // If invoice already exists
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        $course = getCourse((float) $tnx->amount);

        $data = [
            'invoice_no' => ($tnx->id + 2762),
            'date' => Carbon::parse($tnx->created_at)->format('d-m-Y'),
            'customer_name' => $tnx->payer_name,
            'email' => $tnx->payer_email,
            'mobile' => '+91 ' . $tnx->payer_mobile,
            'item_name' => $course['name'],
            'quantity' => 1,
            'amount' => $tnx->amount,
            'utr' => $tnx->payment_id,
        ];

        $pdf = Pdf::loadView('invoice', compact('data'))->setPaper('A4', 'portrait');

        // Make sure directory exists
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Save invoice
        $pdf->save($path);

        // Download newly generated invoice
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function declaration(string $tid)
    {
        $tnx = Transaction::findOrFail($tid);

        $fileName = ids($tnx->id) . '.pdf';
        $path = storage_path('app/public/declaration/' . $fileName);

        // If declaration already exists, open it directly
        if (file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        $data = [
            'declarant_name' => $tnx->payer_name,
            'email' => $tnx->payer_email,
            'phone' => $tnx->payer_mobile,
            'aadhaar_no' => '',
            'address' => '',
            'amount' => $tnx->amount,
            'payment_date' => Carbon::parse($tnx->created_at)->format('d / m / Y'),
            'payment_mode' => '',
            'payment_reference_no' => $tnx->payment_id ?? $tnx->mr_order_id,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.declaration', compact('data'))->setPaper('a4', 'portrait');

        // Make sure directory exists
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Save PDF
        $pdf->save($path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
