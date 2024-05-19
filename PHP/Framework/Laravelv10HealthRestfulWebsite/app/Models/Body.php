<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Body extends Model
{
    use HasFactory;

    protected $fillable = [
        'body_size',
        'calculate_time',
        'user_id',
    ];

    /**
     * The body that belong to the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
