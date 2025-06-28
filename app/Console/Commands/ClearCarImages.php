<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;

class ClearCarImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:clear-images {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all images from all cars';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cars = Car::all();
        
        if ($cars->isEmpty()) {
            $this->info('No cars found in the database.');
            return;
        }

        $totalImages = 0;
        foreach ($cars as $car) {
            $totalImages += $car->getMedia('car_images')->count();
            $totalImages += $car->getMedia('car_license')->count();
        }

        if ($totalImages === 0) {
            $this->info('No car images found to remove.');
            return;
        }

        if (!$this->option('confirm')) {
            if (!$this->confirm("This will remove {$totalImages} images from {$cars->count()} cars. Are you sure?")) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info("Removing images from {$cars->count()} cars...");
        
        $bar = $this->output->createProgressBar($cars->count());
        $bar->start();

        $removedCount = 0;
        
        foreach ($cars as $car) {
            // Remove car images
            $carImages = $car->getMedia('car_images');
            foreach ($carImages as $image) {
                $image->delete();
                $removedCount++;
            }

            // Remove license images
            $licenseImages = $car->getMedia('car_license');
            foreach ($licenseImages as $image) {
                $image->delete();
                $removedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully removed {$removedCount} images from {$cars->count()} cars.");
    }
} 