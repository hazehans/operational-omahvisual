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

    /**
     * Build WhatsApp offer assignment URL.
     */
    public function getWhatsappOfferUrlAttribute(): string
    {
        $crew = $this->crew;
        if (! $crew || ! $crew->phone) {
            return '#';
        }

        $order = $this->eventOrder;
        $eventDate = $order->event_start_date ? Carbon::parse($order->event_start_date)->translatedFormat('d F Y') : '-';
        $loadingDate = $order->loading_date ? Carbon::parse($order->loading_date)->translatedFormat('d F Y') : $eventDate;

        $msg = "Halo {$crew->nickname}, Omah Visual menawarkan penugasan event:\n"
            ."📌 Event: {$order->event_name}\n"
            ."📅 Tgl Loading: {$loadingDate}\n"
            ."🎭 Tgl Acara: {$eventDate}\n"
            ."📍 Lokasi: {$order->venue_name}\n"
            ."💼 Peran: {$this->role_label}\n"
            ."-----------------------------------\n"
            .($this->fee_loading_in > 0 ? '• Loading In: Rp '.number_format((float) $this->fee_loading_in, 0, ',', '.')."\n" : '')
            .($this->fee_loading_out > 0 ? '• Loading Out: Rp '.number_format((float) $this->fee_loading_out, 0, ',', '.')."\n" : '')
            .($this->fee_meal > 0 ? '• Uang Makan: Rp '.number_format((float) $this->fee_meal, 0, ',', '.')."\n" : '')
            .($this->fee_standby > 0 ? '• Standby/Operator: Rp '.number_format((float) $this->fee_standby, 0, ',', '.')."\n" : '')
            .($this->fee_transport > 0 ? '• Transport: Rp '.number_format((float) $this->fee_transport, 0, ',', '.')."\n" : '')
            .($this->fee_bonus > 0 ? '• Bonus/Lembur: Rp '.number_format((float) $this->fee_bonus, 0, ',', '.')."\n" : '')
            .'💰 TOTAL HONOR: Rp '.number_format((float) $this->total_fee, 0, ',', '.')."\n\n"
            .'Mohon konfirmasi kesediaan (ACC/Bisa). Terima kasih! 🙏';

        return $crew->formatWhatsappUrl($msg);
    }

    /**
     * Build WhatsApp schedule reminder URL with venue location & live mobile SPK token link.
     */
    public function getWhatsappReminderUrlAttribute(): string
    {
        $crew = $this->crew;
        if (! $crew || ! $crew->phone) {
            return '#';
        }

        $order = $this->eventOrder;
        $eventDate = $order->event_start_date ? Carbon::parse($order->event_start_date)->translatedFormat('d F Y') : '-';
        $loadingDate = $order->loading_date ? Carbon::parse($order->loading_date)->translatedFormat('d F Y') : $eventDate;
        $loadingTime = $order->loading_time ?? ($order->jam_acara ?? '-');
        $liveUrl = $order->public_token ? route('spk.live', $order->public_token) : null;

        $msg = "Halo {$crew->nickname}! 👋\n\n"
            ."Berikut REMINDER JADWAL TUGAS event bersama *Omah Visual LED*:\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."🎭 *Acara:* {$order->event_name}\n"
            ."📍 *Venue:* {$order->venue_name}\n"
            .($order->venue_address ? "🏢 *Alamat:* {$order->venue_address}\n" : '')
            ."📅 *Tgl Loading:* {$loadingDate} (Jam: {$loadingTime})\n"
            ."🎪 *Tgl Acara:* {$eventDate}\n"
            ."💼 *Tugas/Peran:* {$this->role_label}\n";

        if ($this->spkAssignment && $this->spkAssignment->vehicle_name) {
            $msg .= "🚗 *Armada:* {$this->spkAssignment->vehicle_name}\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($liveUrl) {
            $msg .= "📱 *Portal SPK Live Mobile (Rundown, Titik Maps & Form QC):*\n"
                ."🔗 {$liveUrl}\n\n";
        } elseif ($order->google_maps_url) {
            $msg .= "📍 *Titik Lokasi Google Maps:*\n"
                ."🔗 {$order->google_maps_url}\n\n";
        }

        $msg .= "Mohon hadir tepat waktu dan memastikan kondisi fisik serta perlengkapan siap.\n"
            .'Semoga acara berjalan sukses dan lancar! Terima kasih 🙏✨';

        return $crew->formatWhatsappUrl($msg);
    }
}
