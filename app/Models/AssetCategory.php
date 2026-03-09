<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'depreciation_method',
        'useful_life_years',
        'chart_of_account_id',
        'depreciation_expense_account_id',
        'accumulated_depreciation_account_id',
    ];

    public function assetAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function accumulatedAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depreciation_account_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
