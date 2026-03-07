<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class PurchaseOrderLine extends Model
{
    use HasFactory, HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'unit_id',
        'tax_rate_id',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'decimal:6',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }
}
