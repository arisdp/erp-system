<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class DeliveryOrder extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'sales_order_id',
        'warehouse_id',
        'do_number',
        'delivery_date',
        'status',
        'shipped_by',
        'shipping_method',
        'tracking_number',
        'received_at',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(DeliveryOrderLine::class);
    }
}
