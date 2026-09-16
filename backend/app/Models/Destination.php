<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Destination extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'activation_id',
        'name',
        'slug',
        'description',
        'primary_photo',
        'location',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function activation(): BelongsTo
    {
        return $this->belongsTo(Activation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DestinationPhoto::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Helper to set location from lat/lng
    public function setLocationAttribute($value)
    {
        if (is_array($value) && isset($value['lat'], $value['lng'])) {
            $this->attributes['location'] = "POINT({$value['lng']} {$value['lat']})";
        } elseif (is_string($value) && $value !== '') {
            $this->attributes['location'] = $value;
        }
    }

    // Helper to get location as lat/lng
    public function getLocationAttribute($value)
    {
        if ($value) {
            preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $value, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                return [
                    'lng' => (float) $matches[1],
                    'lat' => (float) $matches[2],
                ];
            }
        }
        return null;
    }
}
