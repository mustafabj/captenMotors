<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carModels = [
            'Toyota Camry', 'Honda Accord', 'BMW 3 Series', 'Mercedes C-Class',
            'Audi A4', 'Volkswagen Passat', 'Ford Focus', 'Nissan Altima',
            'Hyundai Sonata', 'Kia Optima', 'Mazda 6', 'Subaru Legacy'
        ];

        $vehicleCategories = [
            'Sedan', 'SUV', 'Hatchback', 'Coupe', 'Wagon', 'Convertible',
            'Pickup', 'Van', 'Sports Car', 'Luxury Car'
        ];

        $engineTypes = [
            'Gasoline', 'Diesel', 'Hybrid', 'Electric', 'Plug-in Hybrid'
        ];

        $manufacturingPlaces = [
            'Japan', 'Germany', 'USA', 'South Korea', 'France', 'Italy',
            'UK', 'Sweden', 'Spain', 'Canada', 'Mexico', 'China'
        ];

        $statuses = [
            'not_received', 'paint', 'upholstery', 'mechanic', 
            'electrical', 'agency', 'polish', 'ready'
        ];

        return [
            'model' => fake()->randomElement($carModels),
            'vehicle_category' => fake()->randomElement($vehicleCategories),
            'manufacturing_year' => fake()->numberBetween(2015, 2024),
            'place_of_manufacture' => fake()->randomElement($manufacturingPlaces),
            'number_of_keys' => fake()->numberBetween(1, 3),
            'chassis_number' => fake()->unique()->regexify('[A-Z0-9]{17}'),
            'plate_number' => fake()->optional(0.8)->regexify('[A-Z]{2}[0-9]{3}[A-Z]{2}'),
            'engine_capacity' => fake()->randomElement(['1.6L', '2.0L', '2.5L', '3.0L', '1.8L', '2.2L']),
            'engine_type' => fake()->randomElement($engineTypes),
            'purchase_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'purchase_price' => fake()->randomFloat(2, 10000, 40000),
            'insurance_expiry_date' => fake()->dateTimeBetween('now', '+2 years'),
            'expected_sale_price' => fake()->randomFloat(2, 15000, 50000),
            'status' => fake()->randomElement($statuses),
        ];
    }
} 