<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationTestimonial extends Model
{
    protected $fillable = [
        'activation_id',
        'author_name',
        'author_photo',
        'quote',
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
