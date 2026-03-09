<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class StockAdjustment extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'adjustment_number',
        'adjustment_date',
        'status',
        'reason',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(StockAdjustmentLine::class);
    }
}
