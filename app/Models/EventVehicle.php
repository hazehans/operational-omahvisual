<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_order_id',
        'spk_assignment_id',
        'vehicle_id',
        'vehicle_name',
        'plate_number',
        'driver_name',
        'has_etoll',
        'has_kunci_ban',
        'has_stnk',
        'has_kir',
        'has_barcode_bbm',
        'departure_status',
        'return_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'has_etoll' => 'boolean',
            'has_kunci_ban' => 'boolean',
            'has_stnk' => 'boolean',
            'has_kir' => 'boolean',
            'has_barcode_bbm' => 'boolean',
        ];
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    public function spkAssignment(): BelongsTo
    {
        return $this->belongsTo(SpkAssignment::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
