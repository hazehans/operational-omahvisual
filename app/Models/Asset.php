<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'current_location_id',
        'asset_code',
        'serial_number',
        'barcode',
        'ownership',
        'condition',
        'status',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function movementItems(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    public function serviceTickets(): HasMany
    {
        return $this->hasMany(ServiceTicket::class);
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'available' => 'Bagus / Siap Sewa',
            'damaged' => 'Rusak',
            'in_service' => 'Dalam Servis',
            default => ucfirst((string) $this->condition),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_storage' => 'Tersimpan di Gudang',
            'deployed' => 'Sedang Dipakai Event',
            'maintenance' => 'Perbaikan / Workshop',
            'lost' => 'Hilang / Tertinggal',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Auto-generate kode aset unik Omah Visual LED
     * Format: OVL-AST-YYYYMM-XXXX (contoh: OVL-AST-202610-0001)
     */
    public static function generateAssetCode(): string
    {
        $prefix = 'OVL-AST-'.date('Ym').'-';
        $latest = static::where('asset_code', 'LIKE', $prefix.'%')
            ->orderBy('asset_code', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->asset_code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix.$nextNumber;
    }
}
