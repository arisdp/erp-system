<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PurchaseInvoiceLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'purchase_invoice_id',
        'goods_receipt_line_id',
        'product_id',
        'unit_id',
        'tax_rate_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'decimal:6',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function goodsReceiptLine()
    {
        return $this->belongsTo(GoodsReceiptLine::class);
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
