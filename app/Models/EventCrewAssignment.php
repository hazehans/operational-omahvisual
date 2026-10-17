<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_order_id
 * @property int|null $spk_assignment_id
 * @property int $crew_id
 * @property string $role_label
 * @property float|string|null $fee_loading_in
 * @property float|string|null $fee_loading_out
 * @property float|string|null $fee_meal
 * @property float|string|null $fee_standby
 * @property float|string|null $fee_transport
 * @property float|string|null $fee_bonus
 * @property float|string|null $total_fee
 * @property string $status
 * @property string $payment_status
 * @property Carbon|null $payment_date
 * @property string|null $payment_method
 * @property string|null $payment_reference
 * @property string|null $notes
 * @property-read EventOrder $eventOrder
 * @property-read SpkAssignment|null $spkAssignment
 * @property-read Crew $crew
 */
class EventCrewAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_order_id',
        'spk_assignment_id',
        'crew_id',
        'role_label',
        'fee_loading_in',
        'fee_loading_out',
        'fee_meal',
        'fee_standby',
        'fee_transport',
        'fee_bonus',
        'total_fee',
        'status',
        'payment_status',
        'payment_date',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fee_loading_in' => 'decimal:2',
            'fee_loading_out' => 'decimal:2',
            'fee_meal' => 'decimal:2',
            'fee_standby' => 'decimal:2',
            'fee_transport' => 'decimal:2',
            'fee_bonus' => 'decimal:2',
            'total_fee' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (EventCrewAssignment $assignment) {
            $assignment->total_fee = (string) round(
                (float) $assignment->fee_loading_in
                + (float) $assignment->fee_loading_out
                + (float) $assignment->fee_meal
                + (float) $assignment->fee_standby
                + (float) $assignment->fee_transport
                + (float) $assignment->fee_bonus,
                2
            );
        });
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    public function spkAssignment(): BelongsTo
    {
        return $this->belongsTo(SpkAssignment::class);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function getFormattedTotalFeeAttribute(): string
    {
        return 'Rp '.number_format((float) ($this->total_fee ?? 0), 0, ',', '.');
    }
}
