<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'code',
        'name',
        'brand',
        'model_series',
        'specification',
        'tracking_type',
        'sqm_per_unit',
        'min_stock',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'sqm_per_unit' => 'decimal:4',
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function isIndividual(): bool
    {
        return $this->tracking_type === 'individual';
    }

    /**
     * Total stok fisik (semua kondisi)
     */
    public function getTotalPhysicalStockAttribute(): float
    {
        if ($this->isIndividual()) {
            return (float) $this->assets()->whereIn('status', ['in_storage', 'deployed'])->count();
        }

        return (float) $this->stockBalances()->sum('quantity');
    }

    /**
     * Total stok tersedia (Tersedia = Fisik - Rusak - Servis - Dialokasikan)
     */
    public function getTotalAvailableStockAttribute(): float
    {
        if ($this->isIndividual()) {
            return (float) $this->assets()
                ->where('condition', 'available')
                ->where('status', 'in_storage')
                ->count();
        }

        $balance = $this->stockBalances()
            ->where('condition', 'available')
            ->selectRaw('SUM(quantity - allocated_quantity) as available_qty')
            ->first();

        return (float) ($balance->available_qty ?? 0);
    }

    /**
     * Total luasan sqm (jika produk memiliki atribut luas per unit)
     */
    public function calculateTotalSqm(float $units): ?float
    {
        if (!$this->sqm_per_unit) {
            return null;
        }

        return round($units * (float) $this->sqm_per_unit, 2);
    }
}

