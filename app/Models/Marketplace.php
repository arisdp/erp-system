<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class Marketplace extends Model
{
    use HasFactory, HasUuids, MultiTenant, Auditable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
