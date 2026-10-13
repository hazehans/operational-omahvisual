<?php

namespace Database\Factories;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpkAssignment>
 */
class SpkAssignmentFactory extends Factory
{
    protected $model = SpkAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_order_id' => EventOrder::factory(),
            'spk_number' => 'SPK-2026-'.fake()->unique()->numerify('####'),
            'crew_pic_event_id' => User::factory(),
            'crew_pic_event_name' => fake()->name(),
            'crew_1_name' => fake()->firstName(),
            'crew_2_name' => fake()->firstName(),
            'crew_3_name' => fake()->firstName(),
            'crew_4_name' => fake()->firstName(),
            'crew_5_name' => fake()->optional()->firstName(),
            'crew_6_name' => null,
            'crew_7_name' => null,
            'vehicle_name' => fake()->randomElement(['TRAGA 1', 'TRAGA 2', 'TRAGA 3']),
            'has_etoll' => true,
            'has_kunci_ban' => true,
            'has_stnk' => true,
            'has_kir' => true,
            'has_barcode_bbm' => true,
            'departure_pic_event_name' => null,
            'departure_pic_warehouse_name' => null,
            'departure_datetime' => null,
            'departure_status' => 'pending',
            'departure_notes' => null,
            'return_pic_event_name' => null,
            'return_pic_warehouse_name' => null,
            'return_datetime' => null,
            'return_status' => 'pending',
            'return_notes' => null,
        ];
    }
}
