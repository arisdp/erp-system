<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentSequence extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'module_id',
        'prefix',
        'year',
        'current_number',
        'number_length',
    ];
}
