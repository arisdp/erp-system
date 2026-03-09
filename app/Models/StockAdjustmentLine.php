<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StockAdjustmentLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'unit_price',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:6',
        'actual_quantity' => 'decimal:6',
        'difference' => 'decimal:6',
        'unit_price' => 'decimal:2',
    ];

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
