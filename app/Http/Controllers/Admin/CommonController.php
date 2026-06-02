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
}
