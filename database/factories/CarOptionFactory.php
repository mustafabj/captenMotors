<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarOption>
 */
class CarOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carOptions = [
            'Leather Seats', 'Sunroof', 'Navigation System', 'Bluetooth',
            'Backup Camera', 'Heated Seats', 'Ventilated Seats', 'Premium Audio',
            'Alloy Wheels', 'LED Headlights', 'Cruise Control', 'Keyless Entry',
            'Push Button Start', 'Dual Zone Climate Control', 'Power Windows',
            'Power Locks', 'Fog Lights', 'Spoiler', 'Tinted Windows'
        ];

        return [
            'car_id' => Car::factory(),
            'name' => fake()->randomElement($carOptions),
        ];
    }
} 