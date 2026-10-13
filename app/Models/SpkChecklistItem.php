<?php

namespace App\Models;

use Database\Factories\SpkChecklistItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkChecklistItem extends Model
{
    /** @use HasFactory<SpkChecklistItemFactory> */
    use HasFactory;

    protected $fillable = [
        'spk_assignment_id',
        'page_number',
        'category',
        'item_name',
        'item_detail',
        'quantity',
        'unit',
        'checked_out',
        'checked_in',
        'condition_notes',
    ];

    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'quantity' => 'integer',
            'checked_out' => 'boolean',
            'checked_in' => 'boolean',
        ];
    }

    public function spkAssignment(): BelongsTo
    {
        return $this->belongsTo(SpkAssignment::class);
    }
}
