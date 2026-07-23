<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::authUser()
            ->when(request()->filled('search'), function ($q) {
                $q->where(function ($query) {
                    $search = request('search');
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when(request()->filled('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->withTrashed()
            ->whereNotNull('user_id')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $inactive = User::authUser()->whereNotNull('user_id')->where('status', 0)->withTrashed()->count();
        $active = User::authUser()->whereNotNull('user_id')->where('status', 1)->withTrashed()->count();
        $deleted = User::authUser()->whereNotNull('user_id')->onlyTrashed()->count();

        return view('admin.users.list', compact('users', 'inactive', 'active', 'deleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $input = $request->validated();

        $input['password'] = bcrypt($input['password']);
        $input['user_id'] = auth()->id();

        User::create($input);

        return redirect()
            ->route('user.index')
            ->with('res.success', 'New user created successful.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::authUser()->whereNotNull('user_id')->find($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $input = $request->validated();

        if (isset($input['password'])) {

            $input['password'] = bcrypt($input['password']);
        } else {

            unset($input['password']);
        }

        User::whereNotNull('user_id')->authUser()->where('id', $id)->update($input);

        return redirect()
            ->route('user.index')
            ->with('res.success', 'User updated successful.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::whereNotNull('user_id')
            ->authUser()
            ->where('id', $id)
            ->withTrashed()
            ->first();

        if ($user->deleted_at) {
            $user->restore();
            return redirect()
                ->route('user.index')
                ->with('res.success', 'User restored successful.');
        } else {
            $user->delete();
            return redirect()
                ->route('user.index')
                ->with('res.success', 'User deleted successful.');
        }
    }
}
