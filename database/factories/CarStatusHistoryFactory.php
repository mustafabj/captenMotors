<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarStatusHistory>
 */
class CarStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            'not_received', 'paint', 'upholstery', 'mechanic', 
            'electrical', 'agency', 'polish', 'ready'
        ];

        return [
            'car_id' => Car::factory(),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.7)->sentence(),
        ];
    }
} 