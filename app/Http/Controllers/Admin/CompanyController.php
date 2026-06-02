<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $users = User::when(isset($request->search), function ($query) use ($search) {
            $columns = Schema::getColumnListing('users');
            $query->where(function ($query) use ($columns, $search) {
                foreach ($columns as $column) {
                    if (in_array($column, ['id', 'name', 'email', 'mobile', 'timezone', 'device_type', 'device_name'])) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        })
            ->when(isset($request->status), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->withTrashed()
            ->paginate(20);

        $inactive = User::where('status', 0)->withTrashed()->count();
        $active = User::where('status', 1)->withTrashed()->count();
        $deleted = User::onlyTrashed()->count();

        return view('admin.user.list', compact('users', 'inactive', 'deleted', 'active'));
    }

    public function create()
    {
        return view('admin.user.create');
    }
}
