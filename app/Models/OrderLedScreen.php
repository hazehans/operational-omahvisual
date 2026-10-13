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
}
