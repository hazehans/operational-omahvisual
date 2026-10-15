<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'product_id',
        'asset_id',
        'reported_by',
        'technician_id',
        'status',
        'priority',
        'quantity',
        'issue_description',
        'diagnosis',
        'action_taken',
        'source_location_id',
        'workshop_location_id',
        'return_location_id',
        'intake_date',
        'completion_date',
        'notes',
    ];

    protected $casts = [
        'intake_date' => 'datetime',
        'completion_date' => 'datetime',
        'quantity' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'source_location_id');
    }

    public function workshopLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'workshop_location_id');
    }

    public function returnLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'return_location_id');
    }

    public function spareparts(): HasMany
    {
        return $this->hasMany(ServiceSparepart::class);
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'SRV-'.Carbon::now()->format('Ym').'-';
        $latest = self::where('ticket_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if (! $latest) {
            return $prefix.'0001';
        }

        $lastSeq = (int) substr($latest->ticket_number, -4);

        return $prefix.str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_inspection' => 'Antrean Masuk',
            'in_progress' => 'Sedang Dikerjakan',
            'waiting_parts' => 'Menunggu Part',
            'quality_control' => 'Uji Coba & QC',
            'completed' => 'Selesai (Siap Pakai)',
            'scrapped' => 'Afkir / Rusak Total',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending_inspection' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
            'in_progress' => 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30',
            'waiting_parts' => 'bg-purple-500/15 text-purple-400 border border-purple-500/30',
            'quality_control' => 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30',
            'completed' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
            'scrapped' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
            default => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Darurat / Segera',
            default => ucfirst($this->priority),
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
            'normal' => 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
            'high' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
            'urgent' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30 animate-pulse',
            default => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
        };
    }
}
