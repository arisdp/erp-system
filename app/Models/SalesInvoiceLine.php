<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SalesInvoiceLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sales_invoice_id',
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

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
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
