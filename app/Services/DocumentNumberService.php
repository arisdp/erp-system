<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public static function generate($companyId, $moduleId)
    {
        return DB::transaction(function () use ($companyId, $moduleId) {

            $year = date('Y');

            $sequence = DocumentSequence::lockForUpdate()->firstOrCreate(
                [
                    'company_id' => $companyId,
                    'module_id' => $moduleId,
                    'year' => $year,
                ],
                [
                    'prefix' => 'DOC',
                    'current_number' => 0,
                    'number_length' => 4,
                ]
            );

            $sequence->increment('current_number');

            $number = str_pad(
                $sequence->current_number,
                $sequence->number_length,
                '0',
                STR_PAD_LEFT
            );

            return "{$sequence->prefix}-{$year}-{$number}";
        });
    }
}
    