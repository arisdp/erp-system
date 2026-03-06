<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class Module extends Model
{
    use HasFactory, HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
