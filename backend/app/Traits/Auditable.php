<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::updated(function ($model) {
            $model->audit('updated');
        });

        static::deleted(function ($model) {
            $model->audit('deleted');
        });

        // Track restore for soft deletes
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                $model->audit('restored');
            });
        }
    }

    /**
     * Create audit log entry
     */
    public function audit($action)
    {
        // Skip audit if disabled globally
        if (config('audit.enabled') === false) {
            return;
        }

        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $oldValues = $this->getOriginal();
            $newValues = $this->getAttributes();
            
            // Remove timestamps from comparison if configured
            if (config('audit.exclude_timestamps', true)) {
                unset($oldValues['created_at'], $oldValues['updated_at']);
                unset($newValues['created_at'], $newValues['updated_at']);
            }
            
            // Only log if there are actual changes
            if ($oldValues === $newValues) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = $this->getOriginal();
        } elseif ($action === 'created' || $action === 'restored') {
            $newValues = $this->getAttributes();
        }

        AuditLog::create([
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'action' => $action,
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get all audit logs for this model
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model', 'model_type', 'model_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get latest audit log
     */
    public function latestAudit()
    {
        return $this->auditLogs()->first();
    }

    /**
     * Check who created this record
     */
    public function createdBy()
    {
        return $this->auditLogs()
            ->where('action', 'created')
            ->with('user')
            ->first()
            ?->user;
    }

    /**
     * Check who last modified this record
     */
    public function lastModifiedBy()
    {
        return $this->auditLogs()
            ->where('action', 'updated')
            ->with('user')
            ->first()
            ?->user;
    }

    /**
     * Check who deleted this record
     */
    public function deletedBy()
    {
        return $this->auditLogs()
            ->where('action', 'deleted')
            ->with('user')
            ->first()
            ?->user;
    }
}
