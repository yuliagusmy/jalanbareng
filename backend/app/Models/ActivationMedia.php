<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationMedia extends Model
{
    protected $table = 'activation_media';

    protected $fillable = [
        'activation_id',
        'type',
        'file_url',
        'external_url',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function activation(): BelongsTo
    {
        return $this->belongsTo(Activation::class);
    }
}
