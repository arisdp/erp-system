<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class ChartOfAccount extends Model
{
    use HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'company_id',
        'account_type_id',
        'account_group_id',
        'parent_id',
        'account_code',
        'account_name',
        'is_postable',
        'is_active',
    ];

    protected $casts = [
        'is_postable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }

    public function accountGroup()
    {
        return $this->group();
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
