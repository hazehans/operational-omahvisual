<?php

namespace App\Models;

use App\Support\WhatsAppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Invoice extends Model
{
    use HasFactory;

    public const DEFAULT_BANK_INFO = "Bank Central Asia (BCA)\nNo. Rekening: 4390865177\na/n Muhammad Burhanudin\n\nBank Central Asia (BCA)\nNo. Rekening: 3660399282\na/n Balqist Hamada";

    public const DEFAULT_TERMS = "1. Pembayaran DP (Down Payment) minimal 30% - 50% dilakukan sebagai tanda jadi penguncian jadwal dan unit LED.\n2. Pelunasan tagihan selambat-lambatnya dilakukan sebelum acara dimulai atau saat proses loading-in panggung.\n3. Harap sertakan nomor invoice pada berita transfer bank.\n4. Bukti transfer mohon dikonfirmasikan kepada Admin/Finance Omah Visual LED.";

    protected $fillable = [
        'invoice_number',
        'event_order_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'invoice_type',
        'title',
        'subtotal',
        'discount',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'bank_info',
        'notes',
        'terms_conditions',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->latest('payment_date');
    }

    /**
     * Recalculate invoice totals from items and payments, and synchronize with order.
     */
    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('total_price');
        $discount = (float) $this->discount;
        $taxable = max(0, $subtotal - $discount);
        $taxAmount = $this->tax_percent > 0 ? round($taxable * ((float) $this->tax_percent / 100), 2) : 0;
        $totalAmount = $taxable + $taxAmount;

        $paidAmount = (float) $this->payments()->sum('amount');
        $balanceDue = max(0, $totalAmount - $paidAmount);

        $status = $this->status;
        if ($status !== 'cancelled') {
            if ($paidAmount >= $totalAmount && $totalAmount > 0) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }
        }

        $this->updateQuietly([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'status' => $status,
        ]);

        // Synchronize with EventOrder
        if ($this->eventOrder) {
            $orderTotalPaid = (float) InvoicePayment::where('event_order_id', $this->event_order_id)->sum('amount');
            $orderTotal = (float) $this->eventOrder->total_price;

            $orderPaymentStatus = 'unpaid';
            if ($orderTotalPaid >= $orderTotal && $orderTotal > 0) {
                $orderPaymentStatus = 'paid';
            } elseif ($orderTotalPaid > 0) {
                $orderPaymentStatus = 'partial';
            }

            $this->eventOrder->updateQuietly([
                'paid_amount' => $orderTotalPaid,
                'payment_status' => $orderPaymentStatus,
            ]);
        }
    }

    /**
     * Build WhatsApp Click to Chat URL with full invoice billing message.
     */
    public function getWhatsappUrlAttribute(): string
    {
        $phone = $this->customer?->phone;
        if (! $phone) {
            return '#';
        }

        $customerName = $this->customer->name ?? 'Pelanggan';
        $eventName = $this->eventOrder?->event_name ?? $this->title ?? 'Sewa LED Screen';
        $invoiceNum = $this->invoice_number;
        $dueDate = $this->due_date ? Carbon::parse($this->due_date)->translatedFormat('d F Y') : '-';
        $total = 'Rp '.number_format((float) $this->total_amount, 0, ',', '.');
        $paid = 'Rp '.number_format((float) $this->paid_amount, 0, ',', '.');
        $balance = 'Rp '.number_format((float) $this->balance_due, 0, ',', '.');

        $statusText = match ($this->status) {
            'paid' => 'LUNAS',
            'partial' => 'DIBAYAR SEBAGIAN (DP)',
            'cancelled' => 'DIBATALKAN',
            default => 'BELUM DIBAYAR',
        };

        $message = "Halo {$customerName},\n\n";
        $message .= "Berikut kami sampaikan rincian tagihan Invoice dari *Omah Visual LED*:\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📄 *No. Invoice:* {$invoiceNum}\n";
        $message .= "🎭 *Acara:* {$eventName}\n";
        $message .= "📅 *Jatuh Tempo:* {$dueDate}\n";
        $message .= "💰 *Total Tagihan:* {$total}\n";
        $message .= "💵 *Telah Dibayar:* {$paid}\n";
        $message .= "⚠️ *Sisa Tagihan:* {$balance}\n";
        $message .= "📌 *Status:* [{$statusText}]\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "Pembayaran dapat ditransfer melalui rekening resmi Omah Visual:\n";
        $message .= "🏦 *BCA:* 4390865177 a/n *Muhammad Burhanudin*\n";
        $message .= "🏦 *BCA:* 3660399282 a/n *Balqist Hamada*\n\n";
        $message .= "Mohon konfirmasikan bukti transfer setelah melakukan pembayaran.\n";
        $message .= 'Terima kasih atas kerja sama dan kepercayaannya kepada *Omah Visual LED*! 🙏✨';

        return WhatsAppHelper::buildUrl($phone, $message);
    }

    /**
     * Convert number into Indonesian words (Terbilang).
     */
    public static function terbilang(float|int $angka): string
    {
        $val = abs((int) $angka);
        if ($val === 0) {
            return 'Nol';
        }

        $raw = self::convertTerbilang($val);

        return trim(preg_replace('/\s+/', ' ', $raw));
    }

    private static function convertTerbilang(int $angka): string
    {
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            return ' '.$baca[$angka];
        } elseif ($angka < 20) {
            return self::convertTerbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            return self::convertTerbilang(intdiv($angka, 10)).' Puluh'.self::convertTerbilang($angka % 10);
        } elseif ($angka < 200) {
            return ' Seratus'.self::convertTerbilang($angka - 100);
        } elseif ($angka < 1000) {
            return self::convertTerbilang(intdiv($angka, 100)).' Ratus'.self::convertTerbilang($angka % 100);
        } elseif ($angka < 2000) {
            return ' Seribu'.self::convertTerbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::convertTerbilang(intdiv($angka, 1000)).' Ribu'.self::convertTerbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::convertTerbilang(intdiv($angka, 1000000)).' Juta'.self::convertTerbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return self::convertTerbilang(intdiv($angka, 1000000000)).' Miliar'.self::convertTerbilang($angka % 1000000000);
        }

        return self::convertTerbilang(intdiv($angka, 1000000000000)).' Triliun'.self::convertTerbilang($angka % 1000000000000);
    }
}
