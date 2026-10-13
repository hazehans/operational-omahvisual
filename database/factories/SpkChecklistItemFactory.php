<?php

namespace Database\Factories;

use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpkChecklistItem>
 */
class SpkChecklistItemFactory extends Factory
{
    protected $model = SpkChecklistItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'spk_assignment_id' => SpkAssignment::factory(),
            'page_number' => 2,
            'category' => 'KABINET & KELISTRIKAN',
            'item_name' => 'Kabinet LED 1 Mtr SAGA',
            'item_detail' => 'TOTAL 18 SQM, 6 BOX',
            'quantity' => 36,
            'unit' => 'pcs',
            'checked_out' => false,
            'checked_in' => false,
            'condition_notes' => null,
        ];
    }
}
