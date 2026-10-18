<?php

namespace App\Models;

use App\Support\WhatsAppHelper;
use Database\Factories\CrewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string|null $phone
 * @property string|null $emergency_phone
 * @property-read string $whatsapp_number
 * @property-read string $whatsapp_url
 * @property-read string $national_phone
 * @property-read string $national_emergency_phone
 * @property-read string $formatted_phone
 */
class Crew extends Model
{
    /** @use HasFactory<CrewFactory> */
    use HasFactory;

    protected $fillable = [
        'crew_code',
        'nickname',
        'full_name',
        'birth_date',
        'phone',
        'email',
        'emergency_phone',
        'position',
        'positions',
        'employment_type',
        'hris_employee_id',
        'join_date',
        'address',
        'city',
        'photo_path',
        'ktp_photo_path',
        'rate_card_photo_path',
        'default_fee_loading_in',
        'default_fee_loading_out',
        'default_fee_meal',
        'default_fee_standby',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'positions' => 'array',
            'birth_date' => 'date',
            'join_date' => 'date',
            'default_fee_loading_in' => 'decimal:2',
            'default_fee_loading_out' => 'decimal:2',
            'default_fee_meal' => 'decimal:2',
            'default_fee_standby' => 'decimal:2',
        ];
    }

    public function getPositionsListAttribute(): string
    {
        if (is_array($this->positions) && count($this->positions) > 0) {
            return implode(', ', $this->positions);
        }

        return $this->position ?? 'Crew';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventCrewAssignments(): HasMany
    {
        return $this->hasMany(EventCrewAssignment::class);
    }

    public function getWhatsappNumberAttribute(): string
    {
        return WhatsAppHelper::normalizePhone($this->phone, null, false) ?? '';
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

    public function getNationalEmergencyPhoneAttribute(): string
    {
        return WhatsAppHelper::formatNationalNumber($this->emergency_phone);
    }

    public function getFormattedPhoneAttribute(): string
    {
        return WhatsAppHelper::normalizePhone($this->phone, null, true) ?? ($this->phone ?? '');
    }
}
