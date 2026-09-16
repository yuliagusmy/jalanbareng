<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'activation_id',
        'name',
        'slug',
        'type',
        'description',
        'date',
        'poster',
        'youtube_link',
        'registration_link',
        'is_featured',
        'start_point',
        'finish_point',
        'route',
        'distance',
        'estimated_duration',
        'likes_count',
        'comments_count',
        'participants_count',
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_featured' => 'boolean',
        'distance' => 'decimal:2',
        'estimated_duration' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'participants_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activation(): BelongsTo
    {
        return $this->belongsTo(Activation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withTimestamps();
    }

    // Helpers for geographical data
    public function setStartPointAttribute($value)
    {
        if (is_array($value) && isset($value['lat'], $value['lng'])) {
            $this->attributes['start_point'] = "POINT({$value['lng']} {$value['lat']})";
        } elseif (is_string($value) && $value !== '') {
            $this->attributes['start_point'] = $value;
        }
    }

    public function setFinishPointAttribute($value)
    {
        if (is_array($value) && isset($value['lat'], $value['lng'])) {
            $this->attributes['finish_point'] = "POINT({$value['lng']} {$value['lat']})";
        } elseif (is_string($value) && $value !== '') {
            $this->attributes['finish_point'] = $value;
        }
    }

    public function setRouteAttribute($value)
    {
        if (is_array($value)) {
            $points = collect($value)->map(fn($p) => "{$p['lng']} {$p['lat']}")->implode(',');
            $this->attributes['route'] = "LINESTRING($points)";
        } elseif (is_string($value) && $value !== '') {
            $this->attributes['route'] = $value;
        }
    }

    public function getStartPointAttribute($value)
    {
        if ($value) {
            if (preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $value, $matches)) {
                return ['lng' => (float) $matches[1], 'lat' => (float) $matches[2]];
            }
        }
        return null;
    }

    public function getFinishPointAttribute($value)
    {
        if ($value) {
            if (preg_match('/POINT\(([^ ]+) ([^ ]+)\)/', $value, $matches)) {
                return ['lng' => (float) $matches[1], 'lat' => (float) $matches[2]];
            }
        }
        return null;
    }

    public function getRouteAttribute($value)
    {
        if ($value) {
            if (preg_match('/LINESTRING\((.+)\)/', $value, $matches) && isset($matches[1])) {
                return collect(explode(',', $matches[1]))->map(function($point) {
                    $coords = explode(' ', trim($point));
                    if (count($coords) === 2) {
                        return ['lng' => (float) $coords[0], 'lat' => (float) $coords[1]];
                    }
                    return null;
                })->filter()->toArray();
            }
        }
        return null;
    }
}
