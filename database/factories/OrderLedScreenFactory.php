<?php

namespace Database\Factories;

use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderLedScreen>
 */
class OrderLedScreenFactory extends Factory
{
    protected $model = OrderLedScreen::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $width = 6.0;
        $height = 3.0;
        $sqm = $width * $height; // 18 sqm
        $cabinets1m = (int) ($sqm * 2); // 36 cabinets 500x1000mm
        $hardcases = (int) ceil($cabinets1m / 6);

        return [
            'event_order_id' => EventOrder::factory(),
            'screen_name' => 'Main Screen',
            'brand_series' => fake()->randomElement(['SAGA', 'LITE', 'LAMPRO', 'MAVIC']),
            'pixel_pitch' => 'P3.9',
            'width_m' => $width,
            'height_m' => $height,
            'total_sqm' => $sqm,
            'cabinets_1m' => $cabinets1m,
            'cabinets_50cm' => 0,
            'total_cabinets' => $cabinets1m,
            'total_hardcases' => $hardcases,
            'rigging_method' => 'ground_stake',
            'stage_specification' => 'Ground stage level 40cm, kain hitam',
            'processor_model' => 'Novastar VX600',
            'power_jumpers_qty' => $cabinets1m,
            'lan_jumpers_qty' => $cabinets1m,
            'spare_modules_qty' => 2,
            'spare_receiving_cards_qty' => 1,
            'spare_psu_qty' => 1,
            'notes' => null,
        ];
    }
}
