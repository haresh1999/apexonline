<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function pageNotFound()
    {
        return view('admin.errors.404');
    }

    public function serverError()
    {
        return view('admin.errors.500');
    }

    public function oneroyalCallback()
    {
        return response()->json(date('Y-m-d H:i:s'));
    }

    public function apexonlineCallback()
    {
        return response()->json(date('Y-m-d H:i:s'));
    }
}
