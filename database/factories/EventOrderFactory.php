<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventOrder>
 */
class EventOrderFactory extends Factory
{
    protected $model = EventOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loadingDate = fake()->dateTimeBetween('now', '+1 month');
        $eventDate = (clone $loadingDate)->modify('+1 day');

        return [
            'order_number' => 'ORD-2026-'.fake()->unique()->numerify('####'),
            'customer_id' => Customer::factory(),
            'event_name' => fake()->randomElement(['Wedding Party', 'Gathering Perusahaan', 'Konser Musik', 'Seminar Nasional', 'Dies Natalis Kampus']),
            'event_type' => fake()->randomElement(['indoor', 'outdoor', 'semi-outdoor']),
            'venue_name' => fake()->randomElement(['Graha Cakrawala Malang', 'Tunjungan Plaza Surabaya', 'Grand Mercure Malang', 'Hotel Santika Premiere', 'GOR Ken Arok']),
            'venue_address' => fake()->address(),
            'google_maps_url' => 'https://maps.google.com/?q='.fake()->latitude().','.fake()->longitude(),
            'loading_date' => $loadingDate->format('Y-m-d'),
            'loading_time' => fake()->randomElement(['22:00', '23:00', '08:00', '13:00']),
            'ready_time' => '07:00',
            'event_start_date' => $eventDate->format('Y-m-d'),
            'event_end_date' => $eventDate->format('Y-m-d'),
            'event_time' => '09:00 - 16:00',
            'standby_time' => '06:30',
            'electricity_source' => fake()->randomElement(['PLN 16A', 'Genset 40KVA', 'Genset 60KVA']),
            'dress_code' => 'Seragam Omahvisual',
            'status' => 'confirmed',
            'notes' => fake()->optional()->sentence(),
            'total_price' => fake()->numberBetween(3000000, 15000000),
            'down_payment' => 2000000,
            'down_payment_method' => 'BCA Muhammad Burhanudin',
            'paid_amount' => 2000000,
            'payment_status' => 'partial',
            'vehicle_loading_in' => fake()->randomElement(['TRAGA 1', 'TRAGA 2', 'TRAGA 3']),
            'vehicle_loading_out' => fake()->randomElement(['TRAGA 1', 'TRAGA 2', 'TRAGA 3']),
            'vehicle_notes' => null,
            'jimmy_jib' => null,
            'tv_43_qty' => 0,
            'tv_50_qty' => 0,
            'tv_55_qty' => 0,
            'tv_60_qty' => 0,
            'tv_65_qty' => 0,
            'projector_qty' => 0,
            'projector_screen_qty' => 0,
            'public_token' => Str::random(32),
            'created_by' => User::factory(),
        ];
    }
}
