<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;
use App\Core\Traits\MultiTenant;

class FiscalYear extends Model
{
    use HasUuids, MultiTenant, Auditable;

    protected $fillable = [
        'company_id',
        'year',
        'start_date',
        'end_date',
        'is_closed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
