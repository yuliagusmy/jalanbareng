<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CashoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points_requested',
        'rupiah_amount',
        'payment_method',
        'account_number',
        'account_name',
        'status',
        'receipt_image',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'points_requested' => 'integer',
        'rupiah_amount' => 'integer',
        'processed_at' => 'datetime',
    ];

    protected $appends = [
        'receipt_image_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getReceiptImageUrlAttribute(): ?string
    {
        if (!$this->receipt_image) {
            return null;
        }

        if (Str::startsWith($this->receipt_image, ['http://', 'https://'])) {
            return $this->receipt_image;
        }

        return url('storage/' . ltrim($this->receipt_image, '/'));
    }
}
