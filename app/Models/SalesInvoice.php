<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class SalesInvoice extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'sales_order_id',
        'delivery_order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'total_amount',
        'tax_amount',
        'platform_fee',
        'net_amount',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function lines()
    {
        return $this->hasMany(SalesInvoiceLine::class);
    }
}
