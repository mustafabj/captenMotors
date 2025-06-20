<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\CarOption;
use App\Models\CarInspection;
use App\Models\CarStatusHistory;
use App\Models\CarEquipmentCost;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 cars with their options and inspections
        Car::factory(20)->create()->each(function ($car) {
            // Create 2-5 options for each car
            CarOption::factory(fake()->numberBetween(2, 5))->create([
                'car_id' => $car->id
            ]);

            // Create inspection for each car
            CarInspection::factory()->create([
                'car_id' => $car->id
            ]);

            // Create 2-4 status history entries for each car
            CarStatusHistory::factory(fake()->numberBetween(2, 4))->create([
                'car_id' => $car->id
            ]);

            // Create 1-3 equipment costs for each car
            CarEquipmentCost::factory(fake()->numberBetween(1, 3))->create([
                'car_id' => $car->id
            ]);
        });
    }
} 