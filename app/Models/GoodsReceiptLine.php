<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GoodsReceiptLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_line_id',
        'product_id',
        'unit_id',
        'quantity_ordered',
        'quantity_received',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:6',
        'quantity_received' => 'decimal:6',
        'expiry_date' => 'date',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderLine()
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
