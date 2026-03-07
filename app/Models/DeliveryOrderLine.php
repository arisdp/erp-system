<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DeliveryOrderLine extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'delivery_order_id',
        'sales_order_line_id',
        'product_id',
        'unit_id',
        'quantity_ordered',
        'quantity_shipped',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:6',
        'quantity_shipped' => 'decimal:6',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function salesOrderLine()
    {
        return $this->belongsTo(SalesOrderLine::class);
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
