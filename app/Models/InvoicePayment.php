<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'event_order_id',
        'payment_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'proof_image',
        'notes',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'transfer_bca' => 'Bank BCA',
            'transfer_bri' => 'Bank BRI',
            'transfer_mandiri' => 'Bank Mandiri',
            'cash' => 'Tunai / Cash',
            'qris' => 'QRIS',
            default => ucfirst(str_replace('_', ' ', $this->payment_method)),
        };
    }
}
