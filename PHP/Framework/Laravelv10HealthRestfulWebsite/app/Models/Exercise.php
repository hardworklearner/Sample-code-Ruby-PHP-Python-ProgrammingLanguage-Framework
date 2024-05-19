<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_name',
        'calories_burned',
        'duration',
        'exercise_type',
        'exercise_level',
        'exercise_description',
        'exercise_image',
        'category_id',
    ];

    /**
     * The users that belong to the exercise.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
