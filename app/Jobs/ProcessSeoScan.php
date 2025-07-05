<?php

// app/Jobs/ProcessSeoScan.php

namespace App\Jobs;

use App\Models\SeoScan;
use App\Services\SeoScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class ProcessSeoScan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scan;

    public function __construct(SeoScan $scan)
    {
        $this->scan = $scan;
    }

    public function handle(SeoScannerService $scanner)
    {
        // Perform the scan
        $scanner->scan($this->scan);

        // Optional: Update status field
        $this->scan->update(['status' => 'COMPLETED']);
    }
}
