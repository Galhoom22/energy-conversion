<?php

namespace Database\Factories;

use App\Models\Meter;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meter>
 */
class MeterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'serial_number' => fake()->unique()->numerify('MTR-#####'),
            'type' => fake()->randomElement(['electric', 'gas', 'water']),
            'location' => fake()->city(),
            'status' => 'active',
        ];
    }
}
