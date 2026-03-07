<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentSequence extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'type',
        'prefix',
        'year',
        'last_number',
        'number_length',
    ];
}
