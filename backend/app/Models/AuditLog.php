<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user who performed this action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was audited
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Get human-readable action
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'created' => 'Dibuat',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            'restored' => 'Dipulihkan',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action === 'created') {
            return 'Data baru dibuat';
        }

        if ($this->action === 'deleted') {
            return 'Data dihapus';
        }

        if ($this->action === 'updated' && $this->old_values && $this->new_values) {
            $old = $this->old_values;
            $new = $this->new_values;
            $changes = [];

            foreach ($new as $key => $value) {
                if (isset($old[$key]) && $old[$key] != $value) {
                    $changes[] = "$key: '{$old[$key]}' → '{$value}'";
                }
            }

            return implode(', ', $changes);
        }

        return 'Tidak ada perubahan';
    }
}
