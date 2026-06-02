<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {

            return redirect()->route('dashboard');
        }

        return view('admin.auth.login');
    }

    public function loginSubmit(LoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->where('status', 1)
            ->first();

        if (!$user) {
            return back()->with('res.error', 'User not found or inactive');
        }

        if (Auth::attempt($request->except('_token', 'is_remember'), $request->is_remember)) {

            return redirect()
                ->route('dashboard')
                ->with('res.success', 'Login successful');
        }

        return back()->with('res.error', 'Invalid credentials');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()
            ->route('login')
            ->with('res.warning', 'Logged out successfully...!');
    }
}
