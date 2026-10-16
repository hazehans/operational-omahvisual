<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_code',
        'name',
        'plate_number',
        'type',
        'status',
        'special_condition',
        'has_etoll',
        'has_kunci_ban',
        'has_stnk',
        'has_kir',
        'has_barcode_bbm',
        'photo_path',
        'stnk_photo_path',
        'etoll_photo_path',
        'barcode_bbm_photo_path',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'has_etoll' => 'boolean',
            'has_kunci_ban' => 'boolean',
            'has_stnk' => 'boolean',
            'has_kir' => 'boolean',
            'has_barcode_bbm' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
