<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class Permission extends Model
{
    use HasFactory, HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'module_id',
        'name',
        'guard_name',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
