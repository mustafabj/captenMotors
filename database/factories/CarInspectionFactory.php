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
        $inspectionOptions = ['clean_and_free_of_filler', 'painted', 'fully_repainted'];
        $transmissions = ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'];
        $motorConditions = ['Excellent', 'Good', 'Fair', 'Needs Service', 'Recently Serviced'];

        return [
            'car_id' => Car::factory(),
            'hood' => fake()->randomElement($inspectionOptions),
            'front_right_fender' => fake()->randomElement($inspectionOptions),
            'front_left_fender' => fake()->randomElement($inspectionOptions),
            'rear_right_fender' => fake()->randomElement($inspectionOptions),
            'rear_left_fender' => fake()->randomElement($inspectionOptions),
            'trunk_door' => fake()->randomElement($inspectionOptions),
            'front_right_door' => fake()->randomElement($inspectionOptions),
            'rear_right_door' => fake()->randomElement($inspectionOptions),
            'front_left_door' => fake()->randomElement($inspectionOptions),
            'rear_left_door' => fake()->randomElement($inspectionOptions),
            'transmission' => fake()->randomElement($transmissions),
            'motor' => fake()->randomElement($motorConditions),
            'body_notes' => fake()->paragraph(3),
        ];
    }
} 