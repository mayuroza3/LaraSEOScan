<?php

namespace App\Http\Controllers;

use App\Models\SeoScan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeoScanExport;
use App\Jobs\ProcessSeoScan;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreScanRequest;

class SeoScanController extends Controller
{
    public function index()
    {
        $scans = SeoScan::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
            
         // Stats for dashboard
         $scanStats = [
            'total' => SeoScan::where('user_id', auth()->id())->count(),
            'completed' => SeoScan::where('user_id', auth()->id())->where('status', 'COMPLETED')->count(),
            'pending' => SeoScan::where('user_id', auth()->id())->where('status', '!=', 'COMPLETED')->count(),
         ];

        // ✅ Make index show the history, not the form
        return view('scan.history', compact('scans', 'scanStats'));
    }

    public function history()
    {
        return $this->index();
    }

    public function create()
    {
        // This just shows the "New Scan" form
        return view('scan.index');
    }

    public function scan(StoreScanRequest $request)
    {

        $validated = $request->validated();
        $url = $validated['url'];


        // Check if the user is logged in
        $user = Auth::user();

        // Check if the user has exceeded the daily limit
        $todayScanCount = SeoScan::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayScanCount >= 100) {
            return redirect()->back()->withErrors([
                'limit' => '🚫 You have reached your daily scan limit of 5. Please try again tomorrow.',
            ]);
        }

        $sitewideChecks = $this->checkSitewideSeoFiles($request->url);

        // Create a scan entry
        $scan = SeoScan::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'status' => 'QUEUED',
            'has_robots_txt' => $sitewideChecks['robots_txt'],
            'has_sitemap_xml' => $sitewideChecks['sitemap_xml'],
        ]);

        // // Run scan via service class
        // $scanner = new SeoScannerService();
        // $scanner->scan($scan);

        // return redirect()->route('scan.results', $scan->id);

        // Make Jobs to run this SEO Scores
        ProcessSeoScan::dispatch($scan);
        return redirect()->route('scan.history')
            ->with('message', 'Scan submitted! Results will be available shortly.');
    }

    public function results($uuid)
    {
        $scan = SeoScan::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Paginate Issues
        $paginatedIssues = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })
            ->with('page') // Eager load page so we can show URL in issue list
            ->orderByRaw("FIELD(severity, 'critical', 'error', 'warning', 'info')")
            ->paginate(10, ['*'], 'issues_page');

        // Paginate Pages
        $paginatedPages = $scan->pages()
            ->with(['issues', 'links', 'images']) // Eager load relations only for current page
            ->paginate(10, ['*'], 'pages_page');
        
        // Sitewide checks
        $sitewideChecks = [
            'robots_txt' => $scan->has_robots_txt,
            'sitemap_xml' => $scan->has_sitemap_xml,
            'base_url' => parse_url($scan->url, PHP_URL_SCHEME) . '://' . parse_url($scan->url, PHP_URL_HOST)
        ];

        return view('scan.results', compact('scan', 'sitewideChecks', 'paginatedIssues', 'paginatedPages'));
    }

    public function destroy($uuid)
    {
        $scan = SeoScan::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $scan->delete(); // Will soft delete

        return redirect()->route('scan.history')->with('success', 'Scan deleted successfully.');
    }

    public function exportPdf($uuid)
    {
        $scan = SeoScan::with(['pages.links', 'pages.images'])->where('uuid', $uuid)->firstOrFail();
        
        // Increase memory limit for PDF generation
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 120);

        $pdf = Pdf::loadView('exports.scan-pdf', compact('scan'));
        return $pdf->download('seo-scan-' . $scan->uuid . '.pdf');
    }

    public function exportCsv($uuid)
    {
        $scan = SeoScan::where('uuid', $uuid)->firstOrFail();
        return Excel::download(new SeoScanExport($scan->id), 'seo-scan-' . $scan->uuid . '.csv');
    }

    public function status($uuid)
    {
        $scan = SeoScan::where('uuid', $uuid)->where('user_id', Auth::id())->firstOrFail();
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
