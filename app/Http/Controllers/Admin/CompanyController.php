<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Admin\PermissionMiddleware;
use App\Http\Requests\Admin\CompanyRequest;
use App\Models\Gateway;
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
        $gateways = Gateway::where('status', 1)->pluck('name', 'slug');

        return view('admin.user.create', compact('gateways'));
    }

    public function store(CompanyRequest $request)
    {
        $input = $request->validated();

        if (isset($input['password'])) {

            $input['password'] = bcrypt($input['password']);
        }

        if (isset($input['whitelist_ip'])) {

            $input['whitelist_ip'] = json_encode(explode(', ', $input['whitelist_ip']));
        }

        User::create($input);

        return redirect()
            ->route('company.index')
            ->with('res.success', 'New company created successful.');
    }

    public function edit(int $id)
    {
        $user = User::where('id', $id)->first();

        $gateways = Gateway::where('status', 1)->pluck('name', 'slug');

        return view('admin.user.edit', compact('user', 'gateways'));
    }

    public function update(CompanyRequest $request, int $id)
    {
        $input = $request->validated();

        if (filled($request->password)) {
            $input['password'] = bcrypt($request->password);
        } else {
            unset($input['password']);
        }

        if (isset($input['whitelist_ip'])) {
            $input['whitelist_ip'] = json_encode(explode(', ', $input['whitelist_ip']));
        }

        User::where('id', $id)->update($input);

        return redirect()
            ->route('company.index')
            ->with('res.success', 'Company info updated successful.');
    }

    public function destroy(int $id)
    {
        $user = User::where('id', $id)->withTrashed()->first();

        if ($user->trashed()) {

            $user->restore();

            return redirect()
                ->route('company.index')
                ->with('res.success', 'Record successfully restored...!');
        } else {

            $user->delete();

            return redirect()
                ->route('company.index')
                ->with('res.error', 'Record Deleted Successfully...!');
        }
    }
}
