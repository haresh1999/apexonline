<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                        ->orWhere('order_id', 'like', "%{$search}%");
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
                $q->whereDate('created_at', $request->date);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->authTnx()
            ->latest()
            ->paginate(20);

        $total = Transaction::authTnx()->count();
        $pendingTotal = Transaction::authTnx()->where('status', 'pencing')->count();
        $processingTotal = Transaction::authTnx()->where('status', 'processing')->count();
        $completedTotal = Transaction::authTnx()->where('status', 'completed')->count();
        $failedTotal = Transaction::authTnx()->where('status', 'failed')->count();
        $refundedTotal = Transaction::authTnx()->where('status', 'refunded')->count();

        $users = User::pluck('name', 'id');

        return view('admin.transaction.list', compact(
            'tnxs',
            'total',
            'pendingTotal',
            'processingTotal',
            'completedTotal',
            'failedTotal',
            'refundedTotal',
            'users'
        ));
    }
}
