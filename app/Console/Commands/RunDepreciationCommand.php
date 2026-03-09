<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunDepreciationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:run-depreciation {--date= : The date to run depreciation for (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger monthly asset depreciation for all companies via Queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting asset depreciation process dispatch...');

        $date = $this->option('date');
        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            $this->info("Dispatching depreciation job for company: {$company->name}");
            \App\Jobs\RunDepreciationJob::dispatch($company->id, $date);
        }

        $this->info('All depreciation jobs have been dispatched to the queue.');
    }
}
