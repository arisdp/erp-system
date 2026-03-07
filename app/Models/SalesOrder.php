<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class SalesOrder extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_id',
        'marketplace_id',
        'so_number',
        'transaction_type',
        'order_date',
        'status',
        'total_amount',
        'tax_amount',
        'net_amount',
        'platform_fee',
        'platform_discount',
        'platform_voucher',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'platform_discount' => 'decimal:2',
        'platform_voucher' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function lines()
    {
        return $this->hasMany(SalesOrderLine::class);
    }
}
