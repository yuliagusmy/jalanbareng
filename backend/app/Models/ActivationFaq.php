<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationFaq extends Model
{
    protected $fillable = [
        'activation_id',
        'question',
        'answer',
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
