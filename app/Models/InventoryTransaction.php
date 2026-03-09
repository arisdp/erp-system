<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class InventoryTransaction extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'product_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'quantity',
        'unit_price',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'decimal:6',
        'unit_price' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
