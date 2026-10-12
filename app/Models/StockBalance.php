<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'ownership',
        'condition',
        'quantity',
        'allocated_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'allocated_quantity' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity - (float) $this->allocated_quantity);
    }
}
