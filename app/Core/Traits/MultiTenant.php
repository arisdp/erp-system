<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait MultiTenant
{
    protected static function bootMultiTenant()
    {
        if (auth()->check()) {
            static::addGlobalScope('tenant', function (Builder $builder) {
                $builder->where('company_id', auth()->user()->company_id);
                // Optionally scope by branch_id if needed
                // $builder->where('branch_id', auth()->user()->branch_id);
            });

            static::creating(function ($model) {
                if (empty($model->company_id)) {
                    $model->company_id = auth()->user()->company_id;
                }
                if (empty($model->branch_id) && isset(auth()->user()->branch_id)) {
                    $model->branch_id = auth()->user()->branch_id;
                }
            });
        }
    }
}
