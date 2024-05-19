<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_name',
        'calories_provide',
        'meal_time',
        'category_id',
        'food_time',
        'user_id',
        'description',
        'picture',
    ];
    /**
     * The post that belong to the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
