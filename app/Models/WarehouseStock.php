<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Core\Traits\MultiTenant;

class WarehouseStock extends Model
{
    use HasFactory, HasUuids, MultiTenant;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:6',
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
}
