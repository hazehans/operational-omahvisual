<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number',
        'location_id',
        'requested_by',
        'approved_by',
        'status',
        'reason',
        'rejection_reason',
        'proof_attachment_path',
        'stock_movement_id',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public static function generateAdjustmentNumber(): string
    {
        $prefix = 'ADJ-'.date('Ym').'-';
        $latest = static::where('adjustment_number', 'LIKE', $prefix.'%')
            ->orderBy('adjustment_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->adjustment_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix.$nextNumber;
    }
}
