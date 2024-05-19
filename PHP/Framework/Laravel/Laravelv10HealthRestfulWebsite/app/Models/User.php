<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ADMIN_ROLE = 0;
    public static function getRole(string $role): int
    {
        $roles = [
            'admin' => 0,
            'user'  => 1
        ];
        return $roles[$role];
    }

    public function hasRole(string $role): int
    {
        $roles = [
            'admin' => 0,
            'user'  => 1
        ];
        return $roles[$role] == $this->role;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The achievements that belong to the user.
     */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->withPivot('complete_time');
    }

    /**
     * The achievements that belong to the user.
     */
    public function weights(): HasMany
    {
        return $this->hasMany(Weight::class);
    }

    /**
     * The bodies that belong to the user.
     */
    public function bodies(): HasMany
    {
        return $this->hasMany(Body::class);
    }

    /**
     * The categories that belong to the user.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The posts that belong to the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The meals that belong to the user.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * The meals that belong to the user.
     */
    public function diaries(): HasMany
    {
        return $this->hasMany(Diary::class);
    }

    /**
     * The meals that belong to the user.
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class)->withPivot(['exercise_time', 'duration', 'calories_burned']);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN_ROLE;
    }
}
