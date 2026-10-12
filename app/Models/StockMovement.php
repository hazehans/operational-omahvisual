<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_number',
        'type',
        'source_location_id',
        'destination_location_id',
        'reference_type',
        'reference_id',
        'created_by',
        'approved_by',
        'status',
        'notes',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'source_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public static function generateMovementNumber(string $type = 'MOV'): string
    {
        $prefix = strtoupper(substr($type, 0, 3)) . '-' . date('Ym') . '-';
        $latest = static::where('movement_number', 'LIKE', $prefix . '%')
            ->orderBy('movement_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->movement_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
