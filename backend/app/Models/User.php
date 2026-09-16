<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'photo',
        'instagram',
        'facebook',
        'twitter',
        'ban_status',
        'ban_reason',
        'banned_at',
        'ban_until',
        'banned_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_at' => 'datetime',
            'ban_until' => 'datetime',
        ];
    }

    public function getPhotoAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return $value ? Storage::url($value) : null;
    }

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function participatedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withTimestamps();
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    // Role checking helpers
    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isCommunityAdmin(): bool
    {
        return $this->role->name === 'community_admin';
    }

    public function isMember(): bool
    {
        return $this->role->name === 'member';
    }

    public function canCreateEvents(): bool
    {
        return $this->isAdmin() || $this->isCommunityAdmin();
    }

    public function canModerateContent(): bool
    {
        return $this->isAdmin() || $this->isCommunityAdmin();
    }

    // Ban checking helpers
    public function isBanned(): bool
    {
        if (!$this->ban_status) {
            return false;
        }

        // Check if suspend ban has expired
        if ($this->ban_status === 'suspend' && $this->ban_until && now()->isAfter($this->ban_until)) {
            return false;
        }

        return in_array($this->ban_status, ['suspend', 'permanent']);
    }

    public function isRestricted(): bool
    {
        return $this->ban_status === 'restrict';
    }

    public function hasWarning(): bool
    {
        return $this->ban_status === 'warning';
    }

    public function canPost(): bool
    {
        return !$this->isBanned() && !$this->isRestricted();
    }

    public function canComment(): bool
    {
        return !$this->isBanned() && !$this->isRestricted();
    }
}
