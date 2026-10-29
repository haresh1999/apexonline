<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankController extends Controller
{
    public function list(Request $request)
    {
        $banks = BankInfo::with('user')
            ->when($request->filled('search'), function ($q) {
                $q->where('account_number', 'like', "%" . request('search') . "%")
                    ->orWhere('ifsc_code', 'like', "%" . request('search') . "%")
                    ->orWhere('holder_name', 'like', "%" . request('search') . "%")
                    ->orWhere('bank_name', 'like', "%" . request('search') . "%");
            })
            ->authUser()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $total = BankInfo::authUser()->count();

        return view('admin.bank.list', compact(
            'banks',
            'total'
        ));
    }

    public function create()
    {
        return view('admin.bank.create');
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'account_number' => 'required|string|max:30|unique:bank_infos,account_number',
            'confirm_account_number' => 'required|string|max:30|same:account_number',
            'ifsc_code' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        BankInfo::create($input);

        return redirect()->route('bank.index')->with('res.success', 'Bank information added successfully.');
    }

    public function edit(BankInfo $bankInfo)
    {
        return view('admin.bank.edit', compact('bankInfo'));
    }

    public function update(Request $request, BankInfo $bankInfo)
    {
        $input = $request->validate([
            'account_number' => 'required|string|max:30|unique:bank_infos,account_number,' . $bankInfo->id,
            'ifsc_code' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        $bankInfo->update($input);

        return redirect()->route('bank.index')->with('res.success', 'Bank information updated successfully.');
    }

    public function destroy(BankInfo $bankInfo)
    {
        $bankInfo->delete();

        return redirect()->route('bank.index')->with('res.success', 'Bank information deleted successfully.');
    }

    public function apiCreate(Request $request)
    {
        $user = config('services.user');

        $validator = Validator::make($request->all(), [
            'account_number' => ['required', 'string', 'max:30', Rule::unique('bank_infos', 'account_number')->where('user_id', $user['id'])],
            'confirm_account_number' => ['required', 'string', 'max:30', 'same:account_number'],
            'ifsc_code' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
            'holder_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => $validator->errors(),
                'status' => false
            ], 422);
        }

        $input = $validator->validate();

        $input['user_id'] = $user['id'];

        BankInfo::create($input);

        return response()->json([
            'message' => 'Bank info created successful!',
            'status' => true
        ], 200);
    }

    public function apiList(Request $request)
    {
        $user = config('services.user');

        $banks = BankInfo::select('id', 'account_number', 'ifsc_code', 'holder_name', 'bank_name')
            ->when($request->filled('search'), function ($q) {
                $q->where('account_number', 'like', "%" . request('search') . "%")
                    ->orWhere('ifsc_code', 'like', "%" . request('search') . "%")
                    ->orWhere('holder_name', 'like', "%" . request('search') . "%")
                    ->orWhere('bank_name', 'like', "%" . request('search') . "%");
            })
            ->where('user_id', $user['id'])
            ->paginate(50);

        return response()->json([
            'data' => $banks,
            'message' => 'Bank list fetched successful',
            'status' => true,
        ], 200);
    }
}
