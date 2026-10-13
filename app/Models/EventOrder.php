<?php

namespace App\Models;

use Database\Factories\EventOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class EventOrder extends Model
{
    /** @use HasFactory<EventOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'event_name',
        'event_type',
        'venue_name',
        'venue_address',
        'google_maps_url',
        'loading_date',
        'loading_time',
        'ready_time',
        'event_start_date',
        'event_end_date',
        'event_time',
        'standby_time',
        'electricity_source',
        'dress_code',
        'stage_width_m',
        'stage_depth_m',
        'stage_height_m',
        'stage_layout_json',
        'stage_layout_image',
        'stage_layout_front_image',
        'status',
        'notes',
        'total_price',
        'down_payment',
        'down_payment_method',
        'paid_amount',
        'payment_status',
        'vehicle_loading_in',
        'vehicle_loading_out',
        'vehicle_notes',
        'jimmy_jib',
        'tv_43_qty',
        'tv_50_qty',
        'tv_55_qty',
        'tv_60_qty',
        'tv_65_qty',
        'projector_qty',
        'projector_screen_qty',
        'public_token',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'loading_date' => 'date',
            'event_start_date' => 'date',
            'event_end_date' => 'date',
            'total_price' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'stage_width_m' => 'decimal:2',
            'stage_depth_m' => 'decimal:2',
            'stage_height_m' => 'decimal:2',
            'stage_layout_json' => 'array',
            'tv_43_qty' => 'integer',
            'tv_50_qty' => 'integer',
            'tv_55_qty' => 'integer',
            'tv_60_qty' => 'integer',
            'tv_65_qty' => 'integer',
            'projector_qty' => 'integer',
            'projector_screen_qty' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (EventOrder $order) {
            if (empty($order->public_token)) {
                $order->public_token = Str::random(32);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function screens(): HasMany
    {
        return $this->hasMany(OrderLedScreen::class);
    }

    public function spkAssignment(): HasOne
    {
        return $this->hasOne(SpkAssignment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalSqmAttribute(): float
    {
        return (float) $this->screens->sum('total_sqm');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_price - (float) $this->paid_amount);
    }
}
