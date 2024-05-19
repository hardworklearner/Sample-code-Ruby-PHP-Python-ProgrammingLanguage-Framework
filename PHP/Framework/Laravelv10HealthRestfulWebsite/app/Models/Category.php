<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
    ];

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * The posts that belong to the category.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The posts that belong to the category.
     */
    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }

    /**
     * The posts that belong to the category.
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }
}
