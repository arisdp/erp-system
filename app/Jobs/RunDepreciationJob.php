<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunDepreciationJob implements ShouldQueue
{
    use Queueable;

    protected $companyId;
    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct($companyId, $date = null)
    {
        $this->companyId = $companyId;
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\AssetService $assetService): void
    {
        $assetService->runMonthlyDepreciation($this->companyId, $this->date);
    }
}
