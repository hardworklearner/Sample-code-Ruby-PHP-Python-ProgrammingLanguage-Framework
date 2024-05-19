<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Weight extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight',
        'calculate_time',
        'user_id',
    ];

    /**
     * The weight that belong to the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
