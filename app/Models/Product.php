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
        'image_path',
        'tracking_type',
        'sqm_per_unit',
        'min_stock',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'sqm_per_unit' => 'decimal:2',
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

    public function serviceTickets(): HasMany
    {
        return $this->hasMany(ServiceTicket::class);
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
        if (! $this->sqm_per_unit) {
            return null;
        }

        return round($units * (float) $this->sqm_per_unit, 2);
    }

    /**
     * Auto-generate SKU unik untuk Master Barang
     * Format: [PREFIX]-[0001] (Contoh: KBL-0001, VDT-0001, PRD-0001)
     */
    public static function generateSku(?int $categoryId = null, ?string $name = null): string
    {
        $prefix = 'PRD';

        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $slug = strtolower($category->slug ?? $category->name);
                if (str_contains($slug, 'kabel') || str_contains($slug, 'perkabelan')) {
                    $prefix = 'KBL';
                } elseif (str_contains($slug, 'videotron') || str_contains($slug, 'kabinet') || str_contains($slug, 'modul')) {
                    $prefix = 'VDT';
                } elseif (str_contains($slug, 'listrik') || str_contains($slug, 'power')) {
                    $prefix = 'PWR';
                } elseif (str_contains($slug, 'controller') || str_contains($slug, 'processor') || str_contains($slug, 'sistem')) {
                    $prefix = 'SYS';
                } elseif (str_contains($slug, 'tv') || str_contains($slug, 'layar')) {
                    $prefix = 'TV';
                } elseif (str_contains($slug, 'rigging') || str_contains($slug, 'konstruksi')) {
                    $prefix = 'RIG';
                } elseif (str_contains($slug, 'tool') || str_contains($slug, 'peralatan') || str_contains($slug, 'alat')) {
                    $prefix = 'TLS';
                } elseif (str_contains($slug, 'hardcase') || str_contains($slug, 'flightcase') || str_contains($slug, 'penyimpanan')) {
                    $prefix = 'HCS';
                } elseif (str_contains($slug, 'sparepart') || str_contains($slug, 'komponen')) {
                    $prefix = 'SPR';
                } else {
                    $clean = preg_replace('/[^a-zA-Z]/', '', $category->name);
                    $prefix = strtoupper(substr($clean, 0, 3)) ?: 'PRD';
                }
            }
        }

        $latest = static::where('code', 'LIKE', $prefix.'-%')
            ->orderBy('code', 'desc')
            ->first();

        $nextNum = 1;
        if ($latest) {
            $parts = explode('-', $latest->code);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $nextNum = (int) $lastPart + 1;
            }
        }

        do {
            $candidate = sprintf('%s-%04d', $prefix, $nextNum);
            $exists = static::where('code', $candidate)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        return $candidate;
    }

    /**
     * URL Gambar produk jika ada
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return null;
    }
}
