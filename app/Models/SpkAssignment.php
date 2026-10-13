<?php

namespace App\Models;

use Database\Factories\SpkAssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpkAssignment extends Model
{
    /** @use HasFactory<SpkAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'event_order_id',
        'spk_number',
        'crew_pic_event_id',
        'crew_pic_event_name',
        'crew_1_name',
        'crew_2_name',
        'crew_3_name',
        'crew_4_name',
        'crew_5_name',
        'crew_6_name',
        'crew_7_name',
        'vehicle_name',
        'has_etoll',
        'has_kunci_ban',
        'has_stnk',
        'has_kir',
        'has_barcode_bbm',
        'departure_pic_event_name',
        'departure_pic_warehouse_name',
        'departure_datetime',
        'departure_status',
        'departure_notes',
        'return_pic_event_name',
        'return_pic_warehouse_name',
        'return_datetime',
        'return_status',
        'return_notes',
    ];

    protected function casts(): array
    {
        return [
            'has_etoll' => 'boolean',
            'has_kunci_ban' => 'boolean',
            'has_stnk' => 'boolean',
            'has_kir' => 'boolean',
            'has_barcode_bbm' => 'boolean',
            'departure_datetime' => 'datetime',
            'return_datetime' => 'datetime',
        ];
    }

    public function eventOrder(): BelongsTo
    {
        return $this->belongsTo(EventOrder::class);
    }

    public function picEventUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crew_pic_event_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(SpkChecklistItem::class);
    }

    public function getPage2ItemsAttribute()
    {
        return $this->checklistItems->where('page_number', 2);
    }

    public function getPage3ItemsAttribute()
    {
        return $this->checklistItems->where('page_number', 3);
    }
}
