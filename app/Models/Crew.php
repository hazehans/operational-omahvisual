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
        'birth_date',
        'phone',
        'email',
        'emergency_phone',
        'position',
        'positions',
        'employment_type',
        'hris_employee_id',
        'join_date',
        'address',
        'city',
        'photo_path',
        'ktp_photo_path',
        'rate_card_photo_path',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'positions' => 'array',
            'birth_date' => 'date',
            'join_date' => 'date',
        ];
    }

    public function getPositionsListAttribute(): string
    {
        if (is_array($this->positions) && count($this->positions) > 0) {
            return implode(', ', $this->positions);
        }

        return $this->position ?? 'Crew';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
