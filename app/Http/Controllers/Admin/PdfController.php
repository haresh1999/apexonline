<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PdfMail;
use App\Models\Pdf;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'pdf' => ['required', 'file', 'mimes:pdf'],
        ]);

        $pdfName = $request->name . '_' . rand(10000, 99999);

        $pdf = uploadFile($request->pdf, 'pdf', $pdfName);

        $row = Pdf::create([
            'name' => $request->name,
            'path' => $pdf,
        ]);

        $emails = Transaction::where('env', 'production')
            ->where('status', 'completed')
            ->where('created_at', '<', $row->created_at)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->distinct()
            ->pluck('email');

        foreach ($emails as $email) {
            Mail::to($email)->queue(
                new PdfMail($pdf, $request->name)
            );
        }

        return redirect()
            ->route('pdf.index')
            ->with('res.success', 'PDF emails have been queued successfully.');
    }
}
