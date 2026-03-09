<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssetService
{
    /**
     * Calculate and record monthly depreciation for all active assets.
     */
    public function runMonthlyDepreciation($companyId, $date = null)
    {
        $processDate = $date ? Carbon::parse($date) : Carbon::now()->startOfMonth();
        
        $assets = Asset::where('company_id', $companyId)
            ->where('status', 'Active')
            ->where('purchase_date', '<=', $processDate)
            ->get();

        return DB::transaction(function () use ($assets, $processDate, $companyId) {
            $totalProcessed = 0;

            foreach ($assets as $asset) {
                // Check if already depreciated for this month
                $exists = AssetDepreciation::where('asset_id', $asset->id)
                    ->whereMonth('date', $processDate->month)
                    ->whereYear('date', $processDate->year)
                    ->exists();

                if ($exists) continue;

                $asset->load('category');
                $category = $asset->category;

                if (!$category || $category->useful_life_years <= 0) continue;

                // Straight Line Calculation: (Cost - Salvage) / (Life * 12)
                $annualDepreciation = ($asset->purchase_price - $asset->salvage_value) / $category->useful_life_years;
                $monthlyDepreciation = $annualDepreciation / 12;

                // Ensure we don't depreciate more than book value
                if ($asset->current_value <= $asset->salvage_value) {
                    $asset->update(['status' => 'Fully Depreciated']);
                    continue;
                }

                if ($monthlyDepreciation > ($asset->current_value - $asset->salvage_value)) {
                    $monthlyDepreciation = $asset->current_value - $asset->salvage_value;
                }

                // Record Depreciation
                AssetDepreciation::create([
                    'company_id' => $companyId,
                    'asset_id' => $asset->id,
                    'date' => $processDate,
                    'amount' => $monthlyDepreciation,
                ]);

                // Update Asset Current Value
                $asset->decrement('current_value', $monthlyDepreciation);
                
                if($asset->current_value <= $asset->salvage_value) {
                    $asset->update(['status' => 'Fully Depreciated']);
                }

                $totalProcessed++;
                
                // TODO: Integrate with Journaling Service in next step
            }

            return $totalProcessed;
        });
    }
}
