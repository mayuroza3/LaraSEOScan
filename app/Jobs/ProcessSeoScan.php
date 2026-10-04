<?php
namespace App\Jobs;

use App\Models\SeoScan;
use App\Services\SeoScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

use Illuminate\Support\Facades\Log;

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
        $this->scan->update(['status' => 'RUNNING']);

        try {
            // Perform the scan
            $scanner->scan($this->scan);

            // Re-fetch scan state
            $this->scan->refresh();

            if ($this->scan->status !== 'FAILED') {
                $this->scan->calculateAndStoreScore();
                $this->scan->update(['status' => 'COMPLETED']);
            }
        } catch (\Throwable $e) {
            Log::error("ProcessSeoScan job failed for scan #{$this->scan->id}: " . $e->getMessage());
            $this->markAsFailed($e);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->markAsFailed($exception);
    }

    protected function markAsFailed(\Throwable $e): void
    {
        try {
            $this->scan->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
                'failed_at' => now(),
            ]);
        } catch (\Throwable $ex) {
            Log::error("Failed updating failed state on scan #{$this->scan->id}: " . $ex->getMessage());
        }
    }
}
