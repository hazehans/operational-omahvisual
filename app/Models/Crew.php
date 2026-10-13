<?php

namespace App\Models;

use Database\Factories\CrewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Crew extends Model
{
    /** @use HasFactory<CrewFactory> */
    use HasFactory;

    protected $fillable = [
        'crew_code',
        'nickname',
        'full_name',
        'phone',
        'emergency_phone',
        'position',
        'address',
        'city',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
