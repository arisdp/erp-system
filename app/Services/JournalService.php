<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalService
{
    protected $docService;

    public function __construct(DocumentNumberService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Create a balanced Journal Entry.
     */
    public function createJournal(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate Balance
            $totalDebit = collect($data['lines'])->sum('debit');
            $totalCredit = collect($data['lines'])->sum('credit');

            if (abs($totalDebit - $totalCredit) > 0.0001) {
                throw new Exception("Double Entry Error: Debit ($totalDebit) must equal Credit ($totalCredit).");
            }

            // 2. Generate Number if not provided
            $journalNumber = $data['journal_number'] ?? $this->docService->generate(
                'JOURNAL', 
                $data['company_id'], 
                $data['prefix'] ?? 'JV'
            );

            // 3. Create Header
            $journal = JournalEntry::create([
                'company_id' => $data['company_id'],
                'branch_id' => $data['branch_id'],
                'fiscal_year_id' => $data['fiscal_year_id'],
                'journal_number' => $journalNumber,
                'journal_date' => $data['journal_date'],
                'description' => $data['description'] ?? '',
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'status' => 'posted', // ERP standard: often auto-posts if balanced
                'created_by' => auth()->id(),
            ]);

            // 4. Create Lines
            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $line['account_id'],
                    'company_id' => $data['company_id'],
                    'branch_id' => $data['branch_id'],
                    'description' => $line['description'] ?? '',
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }

            return $journal;
        });
    }
}
