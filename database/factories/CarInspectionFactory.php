<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarInspection>
 */
class CarInspectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chassisConditions = ['Good', 'Minor Damage', 'Repaired', 'Excellent', 'Needs Attention'];
        $transmissions = ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'];
        $motorConditions = ['Excellent', 'Good', 'Fair', 'Needs Service', 'Recently Serviced'];

        return [
            'car_id' => Car::factory(),
            'front_chassis_right' => fake()->randomElement($chassisConditions),
            'front_chassis_left' => fake()->randomElement($chassisConditions),
            'rear_chassis_right' => fake()->randomElement($chassisConditions),
            'rear_chassis_left' => fake()->randomElement($chassisConditions),
            'transmission' => fake()->randomElement($transmissions),
            'motor' => fake()->randomElement($motorConditions),
            'body_notes' => fake()->paragraph(3),
        ];
    }
} 