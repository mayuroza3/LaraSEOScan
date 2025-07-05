<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoScan;
use App\Services\SeoScannerService;

class SeoScanController extends Controller
{
    public function index()
    {
        return view('scan.index'); // Blade view with input form
    }

    public function scan(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        // Create a scan entry
        $scan = SeoScan::create([
            'url' => $request->url,
            'status' => 'PENDING'
        ]);

        // Run scan via service class
        $scanner = new SeoScannerService();
        $scanner->scan($scan);

        return redirect()->route('scan.results', $scan->id);
    }

    public function results($id)
    {
        $scan = SeoScan::with('pages.links', 'pages.images')->findOrFail($id);
        return view('scan.results', compact('scan'));
    }
}
