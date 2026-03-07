<?php

namespace App\Traits;

use App\Models\TaxRate;

trait HasTaxCalculation
{
    /**
     * Calculate tax amount for a given subtotal and tax rate ID.
     *
     * @param float $subtotal
     * @param string|null $taxRateId
     * @return float
     */
    public function calculateTax($subtotal, $taxRateId = null)
    {
        if (empty($taxRateId)) {
            return 0;
        }

        $taxRate = TaxRate::find($taxRateId);
        
        if (!$taxRate) {
            return 0;
        }

        return ($subtotal * $taxRate->rate) / 100;
    }

    /**
     * Calculate net amount for a document.
     *
     * @param float $totalAmount
     * @param float $taxAmount
     * @param array $adjustments Key-value pair of adjustments (e.g., ['fee' => 10, 'discount' => 5])
     * @return float
     */
    public function calculateNet($totalAmount, $taxAmount, array $adjustments = [])
    {
        $net = $totalAmount + $taxAmount;

        foreach ($adjustments as $type => $value) {
            if (str_contains(strtolower($type), 'discount') || str_contains(strtolower($type), 'fee')) {
                $net -= $value;
            } elseif (str_contains(strtolower($type), 'voucher') || str_contains(strtolower($type), 'tax')) {
                // Voucher adds to the net received (it's covered by platform)
                $net += $value;
            }
        }

        return $net;
    }
}
