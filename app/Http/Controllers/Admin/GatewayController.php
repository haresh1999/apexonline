<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GatewayRequest;
use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $gateways = Gateway::withCount('transactions')
            ->when($request->filled('search'), function ($q) {
                $q->where('name', 'like', "%" . request('search') . "%");
            })
            ->when($request->filled('status'), function ($q) {
                $q->where('status', request('status'));
            })
            ->orderBy('name', 'desc')
            ->paginate(20);

        $total = Gateway::count();
        $active = Gateway::where('status', 1)->count();
        $inactive = Gateway::where('status', 0)->count();

        return view('admin.gateway.list', compact('gateways', 'total', 'active', 'inactive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gateway.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GatewayRequest $request)
    {
        $input = $request->validated();

        $input['slug'] = str()->slug($input['name']);

        Gateway::create($input);

        return redirect()
            ->route('pg.index')
            ->with('res.success', 'Payment gateway created successful.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gateway = Gateway::where('id', $id)->first();

        return view('admin.gateway.edit', compact('gateway'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GatewayRequest $request, string $id)
    {
        $input = $request->validated();

        $input['slug'] = str()->slug($input['name']);

        Gateway::where('id', $id)->update($input);

        return redirect()
            ->route('pg.index')
            ->with('res.success', 'Payment gateway updated successful.');
    }
}
