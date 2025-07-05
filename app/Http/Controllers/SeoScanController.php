<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoScan;
use App\Services\SeoScannerService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeoScanExport;

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


        // Check if the user is logged in
        $user = Auth::user();

        // Check if the user has exceeded the daily limit
        $todayScanCount = SeoScan::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayScanCount >= 5) {
            return redirect()->back()->withErrors([
                'limit' => '🚫 You have reached your daily scan limit of 5. Please try again tomorrow.',
            ]);
        }
        // Create a scan entry
        $scan = SeoScan::create([
            'user_id' => $user->id,
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
        $scan = SeoScan::with('pages.links', 'pages.images')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('scan.results', compact('scan'));
    }

    public function history()
    {
        $scans = SeoScan::where('user_id', Auth::id())
            ->whereNull('deleted_at') // support soft deletes
            ->latest()
            ->paginate(10); // Optional: paginate results

        return view('scan.history', compact('scans'));
    }

    public function destroy($id)
    {
        $scan = SeoScan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $scan->delete(); // Will soft delete

        return redirect()->route('scan.history')->with('success', 'Scan deleted successfully.');
    }

    public function exportPdf($id)
    {
        $scan = SeoScan::with('pages.links', 'pages.images')->findOrFail($id);

        $pdf = Pdf::loadView('exports.scan-pdf', compact('scan'));

        return $pdf->download('seo-scan-' . $scan->id . '.pdf');
    }

    public function exportCsv($id)
    {
        return Excel::download(new SeoScanExport($id), 'seo-scan-' . $id . '.csv');
    }

}
