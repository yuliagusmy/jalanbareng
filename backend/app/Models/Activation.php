<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Activation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'city',
        'city_latitude',
        'city_longitude',
        'short_title',
        'tagline',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'icon',
        'color_theme',
        'description',
        'cta_primary_label',
        'cta_primary_url',
        'cta_secondary_label',
        'cta_secondary_url',
        'contact_person',
        'contact_phone',
        'social_instagram',
        'social_twitter',
        'social_linktree',
        'show_schedule',
        'show_gallery',
        'show_testimonials',
        'show_faq',
        'is_featured',
        'is_active',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'show_schedule' => 'boolean',
        'show_gallery' => 'boolean',
        'show_testimonials' => 'boolean',
        'show_faq' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ActivationMedia::class)->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ActivationFaq::class)->orderBy('sort_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(ActivationTestimonial::class)->orderBy('sort_order');
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getUpcomingEventsAttribute()
    {
        return $this->events()
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getPastEventsAttribute()
    {
        return $this->events()
            ->where('date', '<', now())
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getNextEventAttribute()
    {
        return $this->events()
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->first();
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activation) {
            if (empty($activation->slug)) {
                $activation->slug = Str::slug($activation->name);
            }
        });

        static::updating(function ($activation) {
            if ($activation->isDirty('name') && empty($activation->slug)) {
                $activation->slug = Str::slug($activation->name);
            }
        });
    }
}
