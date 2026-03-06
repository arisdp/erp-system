<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class AccountType extends Model
{
    use HasUuids, Auditable;

    protected $fillable = [
        'code',
        'name',
        'normal_balance',
    ];

    public function accountGroups()
    {
        return $this->hasMany(AccountGroup::class);
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }
}
