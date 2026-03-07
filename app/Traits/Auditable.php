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
        try {
            AuditLog::create([
                'company_id' => $model->company_id ?? null,
                'user_id' => Auth::id(),
                'event' => $event,
                'auditable_type' => get_class($model),
                'auditable_id' => (string) $model->id,
                'old_values' => $model->getOriginal() ?: null,
                'new_values' => $model->getAttributes(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail in migration/seeding if AuditLog fails
            \Log::warning("AuditLog failed: " . $e->getMessage());
        }
    }
}
