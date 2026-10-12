<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'count_number',
        'title',
        'location_id',
        'conducted_by',
        'verified_by',
        'status',
        'notes',
        'finalized_at',
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryCountItem::class);
    }

    public static function generateCountNumber(): string
    {
        $prefix = 'OP-'.date('Ym').'-';
        $latest = static::where('count_number', 'LIKE', $prefix.'%')
            ->orderBy('count_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->count_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix.$nextNumber;
    }
}
