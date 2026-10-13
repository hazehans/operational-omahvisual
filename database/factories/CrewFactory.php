<?php

namespace Database\Factories;

use App\Models\Crew;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Crew>
 */
class CrewFactory extends Factory
{
    protected $model = Crew::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crew_code' => 'CREW-'.fake()->unique()->numerify('####'),
            'nickname' => fake()->firstName(),
            'full_name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'emergency_phone' => '08'.fake()->numerify('##########'),
            'position' => fake()->randomElement(['PIC', 'CREW', 'OPERATOR']),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'is_active' => true,
            'user_id' => null,
        ];
    }
}
