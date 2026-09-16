<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'photo',
        'icon',
    ];

    protected $appends = ['photo_url'];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    /**
     * Get the full URL for the category photo
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        // If it's already a full URL, return it
        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        // Return the full storage URL
        return Storage::disk('public')->url($this->photo);
    }
}
