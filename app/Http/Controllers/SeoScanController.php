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
use App\Jobs\ProcessSeoScan;
use Illuminate\Support\Facades\Http;

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

        $sitewideChecks = $this->checkSitewideSeoFiles($request->url);

        // Create a scan entry
        $scan = SeoScan::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'status' => 'PENDING',
            'has_robots_txt' => $sitewideChecks['robots_txt'],
            'has_sitemap_xml' => $sitewideChecks['sitemap_xml'],
        ]);

        // // Run scan via service class
        // $scanner = new SeoScannerService();
        // $scanner->scan($scan);

        // return redirect()->route('scan.results', $scan->id);

        // Make Jobs to run this SEO Scores
        ProcessSeoScan::dispatch($scan); // Queue the job
        return redirect()->route('scan.history')
            ->with('message', 'Scan submitted! Results will be available shortly.');
    }

    public function results($id)
    {
        $scan = SeoScan::with('pages.links', 'pages.images')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $sitewideChecks = $this->checkSitewideSeoFiles($scan->pages->first()->url);
        return view('scan.results', compact('scan', 'sitewideChecks'));
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

    public function status($id)
    {
        $scan = SeoScan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('scan.status', compact('scan'));
    }

    // 👇 Define this helper method within the controller class
    protected function checkSitewideSeoFiles($url)
    {
        try {
            $parsed = parse_url($url);
            $baseUrl = $parsed['scheme'] . '://' . $parsed['host'];

            $robotsResponse = Http::timeout(5)->get($baseUrl . '/robots.txt');
            $sitemapResponse = Http::timeout(5)->get($baseUrl . '/sitemap.xml');

            return [
                'robots_txt' => $robotsResponse->successful(),
                'sitemap_xml' => $sitemapResponse->successful(),
                'base_url' => $baseUrl,
            ];
        } catch (\Exception $e) {
            return [
                'robots_txt' => false,
                'sitemap_xml' => false,
                'base_url' => null,
            ];
        }
    }
}
