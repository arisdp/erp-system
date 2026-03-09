<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Record an inventory transaction and update warehouse stock.
     * This method expects to be called within a database transaction.
     *
     * @param string $companyId
     * @param string $warehouseId
     * @param string $productId
     * @param string $transactionType ('GRN', 'DO', 'Transfer In', 'Transfer Out', 'Adjustment', 'Opening Balance')
     * @param float $quantity (Positive for IN, Negative for OUT)
     * @param float $unitPrice
     * @param string|null $referenceType
     * @param string|null $referenceId
     * @param string|null $notes
     * @param string|null $date
     * @return InventoryTransaction
     */
    public function recordTransaction(
        $companyId,
        $warehouseId,
        $productId,
        $transactionType,
        $quantity,
        $unitPrice = 0,
        $referenceType = null,
        $referenceId = null,
        $notes = null,
        $date = null
    ) {
        $date = $date ?? now();

        // 1. Create the detailed ledger entry
        $transaction = InventoryTransaction::create([
            'company_id' => $companyId,
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'transaction_type' => $transactionType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'transaction_date' => $date,
            'notes' => $notes,
        ]);

        // 2. Update the dynamic balance (WarehouseStock)
        $stock = WarehouseStock::firstOrCreate(
            [
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
            ],
            [
                'quantity' => 0,
            ]
        );

        // We use raw increment/decrement on an explicit row lock if needed,
        // but since we are in a transaction and firstOrCreate fetched it:
        // A safer way is using lockForUpdate to prevent race conditions:
        $lockedStock = WarehouseStock::where('id', $stock->id)->lockForUpdate()->first();
        
        $lockedStock->quantity += $quantity;
        $lockedStock->save();

        return $transaction;
    }
}
