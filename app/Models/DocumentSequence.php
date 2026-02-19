<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSequence extends Model
{
    protected $fillable = [
        'company_id',
        'module_id',
        'prefix',
        'year',
        'current_number',
        'number_length',
    ];
}
