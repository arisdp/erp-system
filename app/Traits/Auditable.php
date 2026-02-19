<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::storeAudit($model, 'created');
        });

        static::updated(function ($model) {
            self::storeAudit($model, 'updated');
        });

        static::deleted(function ($model) {
            self::storeAudit($model, 'deleted');
        });
    }

    protected static function storeAudit($model, $event)
    {
        AuditLog::create([
            'company_id' => $model->company_id ?? null,
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $model->getOriginal(),
            'new_values' => $model->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
