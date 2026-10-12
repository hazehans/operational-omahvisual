<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_movement_id',
        'product_id',
        'asset_id',
        'ownership',
        'condition',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function movement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
