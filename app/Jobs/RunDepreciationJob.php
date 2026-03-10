<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunDepreciationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
