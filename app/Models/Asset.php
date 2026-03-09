<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'category_id',
        'purchase_date',
        'purchase_price',
        'salvage_value',
        'current_value',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }
}
