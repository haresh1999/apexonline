<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $total = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->sum('amount');

        $pendingTotal = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'pending')
            ->sum('amount');

        $processingTotal = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'processing')
            ->sum('amount');

        $completedTotal = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'completed')
            ->sum('amount');

        $failedTotal = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'failed')
            ->sum('amount');

        $refundedTotal = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'refunded')
            ->sum('amount');

        $count = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->count();

        $pendingCount = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'pending')
            ->count();

        $processingCount = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'processing')
            ->count();

        $completedCount = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'completed')
            ->count();

        $failedCount = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'failed')
            ->count();

        $refundedCount = Transaction::authTnx()
            ->where('env', 'production')
            ->when($request->filled('date'), function ($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->where('status', 'refunded')
            ->count();

        return view('admin.starter', compact(
            'total',
            'pendingTotal',
            'processingTotal',
            'completedTotal',
            'failedTotal',
            'refundedTotal',
            'count',
            'pendingCount',
            'processingCount',
            'completedCount',
            'failedCount',
            'refundedCount',
        ));
    }

    public function profile(Request $request)
    {
        if ($request->isMethod('get')) {

            $user = auth()->user();

            return view('admin.profile', compact('user'));
        }

        $input = $request->validate([
            'name' => ['required', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'mobile' => ['required', 'digits_between:9,12'],
            'password' => ['nullable', 'min:6'],
            'confirm_password' => ['nullable', 'same:password'],
        ]);

        $user = auth()->user();

        if (isset($input['password'])) {

            $user->password = bcrypt($input['password']);
        }

        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->mobile = $input['mobile'];
        $user->save();

        return redirect()
            ->back()
            ->with('res.success', 'Profile updated successfully');
    }
}
