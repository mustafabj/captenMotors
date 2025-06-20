<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarEquipmentCost>
 */
class CarEquipmentCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $equipmentTypes = [
            'Engine Repair', 'Transmission Service', 'Brake System', 'Suspension',
            'Electrical System', 'Air Conditioning', 'Paint Job', 'Interior Work',
            'Wheel Alignment', 'Oil Change', 'Filter Replacement', 'Battery',
            'Tire Replacement', 'Exhaust System', 'Fuel System', 'Cooling System'
        ];

        return [
            'car_id' => Car::factory(),
            'description' => fake()->randomElement($equipmentTypes),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'cost_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'notes' => fake()->optional(0.6)->sentence(),
        ];
    }
} 