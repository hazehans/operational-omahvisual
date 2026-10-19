<?php

namespace App\Models;

use Database\Factories\OrderLedScreenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLedScreen extends Model
{
    /** @use HasFactory<OrderLedScreenFactory> */
    use HasFactory;

    protected $fillable = [
        'event_order_id',
        'screen_name',
        'brand_series',
        'pixel_pitch',
        'width_m',
        'height_m',
        'total_sqm',
        'cabinets_1m',
        'cabinets_50cm',
        'total_cabinets',
        'total_hardcases',
        'rigging_method',
        'stage_specification',
        'processor_model',
        'power_jumpers_qty',
        'lan_jumpers_qty',
        'spare_modules_qty',
        'spare_receiving_cards_qty',
        'spare_psu_qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'width_m' => 'decimal:2',
            'height_m' => 'decimal:2',
            'total_sqm' => 'decimal:2',
            'cabinets_1m' => 'integer',
            'cabinets_50cm' => 'integer',
            'total_cabinets' => 'integer',
            'total_hardcases' => 'integer',
            'power_jumpers_qty' => 'integer',
            'lan_jumpers_qty' => 'integer',
            'spare_modules_qty' => 'integer',
            'spare_receiving_cards_qty' => 'integer',
            'spare_psu_qty' => 'integer',
        ];
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    /**
     * Approximate pixels per linear meter based on pixel pitch.
     */
    public function getPixelsPerMeterAttribute(): int
    {
        $pitch = strtoupper($this->pixel_pitch ?? 'P3.9');

        if (str_contains($pitch, '2.6')) {
            return 384;
        }

        if (str_contains($pitch, '2.9')) {
            return 336;
        }

        if (str_contains($pitch, '4.8')) {
            return 208;
        }

        return 256; // Standard P3.9 (128 pixels per 0.5m cabinet)
    }

    /**
     * Native pixel resolution of this screen.
     *
     * @return array{width: int, height: int, formatted: string}
     */
    public function getPixelResolutionAttribute(): array
    {
        $pxPerM = $this->pixels_per_meter;
        $widthPx = (int) round((float) $this->width_m * $pxPerM);
        $heightPx = (int) round((float) $this->height_m * $pxPerM);

        return [
            'width' => $widthPx,
            'height' => $heightPx,
            'formatted' => "{$widthPx} × {$heightPx} px",
        ];
    }

    /**
     * Calculate screen aspect ratio string (e.g. 16:9, 4:3, or custom ratio).
     */
    public function getAspectRatioAttribute(): string
    {
        $w = (float) $this->width_m;
        $h = (float) $this->height_m;

        if ($h <= 0) {
            return '-';
        }

        $ratio = $w / $h;

        if (abs($ratio - (16 / 9)) < 0.08) {
            return '16:9 (~Standar HD)';
        }

        if (abs($ratio - (4 / 3)) < 0.08) {
            return '4:3 (~Standar Box)';
        }

        if (abs($ratio - (21 / 9)) < 0.1) {
            return '21:9 (Ultrawide)';
        }

        // Reduced fraction approximation
        $gcd = function ($a, $b) use (&$gcd) {
            return ($a % $b) ? $gcd($b, $a % $b) : $b;
        };

        $wInt = (int) round($w * 10);
        $hInt = (int) round($h * 10);

        if ($wInt > 0 && $hInt > 0) {
            $divisor = $gcd($wInt, $hInt);
            $simpleW = $wInt / $divisor;
            $simpleH = $hInt / $divisor;

            if ($simpleW <= 30 && $simpleH <= 30) {
                return "{$simpleW}:{$simpleH}";
            }
        }

        return number_format($ratio, 2).':1';
    }
}
