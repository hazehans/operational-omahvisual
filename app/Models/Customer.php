<?php

namespace App\Models;

use App\Support\WhatsAppHelper;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string|null $phone
 * @property-read string $whatsapp_number
 * @property-read string $whatsapp_url
 * @property-read string $national_phone
 */
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'full_name',
        'birth_date',
        'organization',
        'category',
        'phone',
        'email',
        'address',
        'province',
        'city',
        'pic_secondary_name',
        'pic_secondary_phone',
        'pic_secondary_relation',
        'registration_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registration_date' => 'date',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(EventOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getWhatsappNumberAttribute(): string
    {
        return WhatsAppHelper::normalizeIndonesianPhone($this->phone, false) ?? '';
    }

    public function getWhatsappUrlAttribute(): string
    {
        return WhatsAppHelper::buildUrl($this->phone);
    }

    public function formatWhatsappUrl(string $message = ''): string
    {
        return WhatsAppHelper::buildUrl($this->phone, $message);
    }

    public function getNationalPhoneAttribute(): string
    {
        return WhatsAppHelper::formatNationalNumber($this->phone);
    }
}
