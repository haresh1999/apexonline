<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ButtonGateway;
use Illuminate\Http\Request;

class BtnGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bgs = ButtonGateway::when(request()->filled('status'), function ($q) {
            if (request('status') == 'trashed') {
                $q->onlyTrashed();
            } else {
                $q->withTrashed()->where('status', request('status'));
            }
        }, function ($q) {
            $q->withTrashed();
        })
            ->orderBy('id', 'desc')
            ->get();

        $active = ButtonGateway::where('status', 1)->withTrashed()->count();
        $inactive = ButtonGateway::where('status', 0)->withTrashed()->count();
        $deleted = ButtonGateway::onlyTrashed()->count();

        return view('admin.btn_gateway.list', compact(
            'bgs',
            'active',
            'inactive',
            'deleted'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.btn_gateway.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:button_gateways,name'],
            'url' => ['required', 'url', 'max:255', 'unique:button_gateways,url'],
            'status' => ['required', 'in:0,1']
        ]);

        ButtonGateway::create($request->only('name', 'url', 'status'));

        return redirect()->route('bg.index')->with('res.success', 'Button gateway created successful');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bg = ButtonGateway::find($id);

        return view('admin.btn_gateway.edit', compact('bg'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:button_gateways,name,' . $id],
            'url' => ['required', 'url', 'max:255', 'unique:button_gateways,url,' . $id],
            'status' => ['required', 'in:0,1']
        ]);

        ButtonGateway::where('id', $id)->update($request->only(['name', 'url', 'status']));

        return redirect()->route('bg.index')->with('res.success', 'Button gateway updated successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bg = ButtonGateway::where('id', $id)->withTrashed()->first();

        if ($bg->deleted_at) {
            $bg->restore();
            return redirect()
                ->route('bg.index')
                ->with('res.success', 'Button gateway restored successful.');
        } else {
            $bg->delete();
            return redirect()
                ->route('bg.index')
                ->with('res.error', 'Button gateway deleted successful.');
        }
    }
}
