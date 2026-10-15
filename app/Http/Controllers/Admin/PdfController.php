<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function index()
    {
        $pdfs = Pdf::orderBy('id', 'desc')->paginate(20);

        $total = Pdf::count();

        return view('admin.pdf.list', compact('pdfs', 'total'));
    }

    public function create()
    {
        return view('admin.pdf.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255', 'min:3'],
            'pdf' => ['required', 'file', 'mimes:pdf']
        ]);

        $pdfName = $request->name . '_' . rand(10000, 99999);

        $pdf = uploadFile($request->pdf, 'pdf', $pdfName);

        Pdf::create([
            'name' => $request->name,
            'path' => $pdf
        ]);

        return redirect()->route('pdf.index');
    }
}
