<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class AccountGroup extends Model
{
    use HasUuids, MultiTenant, Auditable;

    protected $fillable = [
        'company_id',
        'account_type_id',
        'code',
        'name',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
