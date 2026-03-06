<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate next document number for a company.
     * Format: [PREFIX]-[YEAR]-[SEQUENCE]
     */
    public function generate(string $type, string $companyId, string $prefix): string
    {
        return DB::transaction(function () use ($type, $companyId, $prefix) {
            $year = date('Y');
            
            $sequence = DocumentSequence::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'type' => $type,
                    'year' => $year,
                ],
                [
                    'prefix' => $prefix,
                    'last_number' => 0,
                ]
            );

            // Row-level lock for concurrency safety
            $sequenceValue = DB::table('document_sequences')
                ->where('id', $sequence->id)
                ->lockForUpdate()
                ->first();

            $nextNumber = $sequenceValue->last_number + 1;
            $formattedNumber = $prefix . '-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            DB::table('document_sequences')
                ->where('id', $sequence->id)
                ->update(['last_number' => $nextNumber]);

            return $formattedNumber;
        });
    }
}