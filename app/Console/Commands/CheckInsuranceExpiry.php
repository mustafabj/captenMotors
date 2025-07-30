<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\InsuranceExpiryNotification;
use App\Models\User;
use Carbon\Carbon;

class CheckInsuranceExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insurance:check-expiry {--days=30 : Number of days to check ahead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for cars with expiring insurance and create notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Checking for insurance expiry within {$days} days...");

        // Get all admin users to notify
        $adminUsers = User::getAdmins();
        
        if ($adminUsers->isEmpty()) {
            $this->warn('No admin users found to notify.');
            return 1;
        }

        $notificationsCreated = 0;
        $carsChecked = 0;

        // Check for expired insurance
        $expiredCars = Car::insuranceExpired()->get();
        foreach ($expiredCars as $car) {
            $this->createInsuranceNotification($car, 'expired', $adminUsers);
            $notificationsCreated += $adminUsers->count();
            $carsChecked++;
        }

        // Check for expiring insurance (within specified days)
        $expiringCars = Car::insuranceExpiringSoon($days)->get();
        foreach ($expiringCars as $car) {
            $daysUntilExpiry = $car->getDaysUntilInsuranceExpiry();
            
            if ($daysUntilExpiry >= -7) {
                // Critical: expiring within 7 days
                $this->createInsuranceNotification($car, 'critical', $adminUsers);
                $notificationsCreated += $adminUsers->count();
            } elseif ($daysUntilExpiry >= -30) {
                // Warning: expiring within 30 days
                $this->createInsuranceNotification($car, 'warning', $adminUsers);
                $notificationsCreated += $adminUsers->count();
            }
            
            $carsChecked++;
        }

        $this->info("Checked {$carsChecked} cars with insurance dates.");
        $this->info("Created {$notificationsCreated} notifications.");

        return 0;
    }

    /**
     * Create insurance expiry notification for all admin users
     */
    private function createInsuranceNotification(Car $car, string $type, $adminUsers)
    {
        $daysUntilExpiry = $car->getDaysUntilInsuranceExpiry();
        
        foreach ($adminUsers as $user) {
            // Check if notification already exists for this car, user, and type
            $existingNotification = InsuranceExpiryNotification::where([
                'car_id' => $car->id,
                'user_id' => $user->id,
                'notification_type' => $type,
            ])->whereDate('created_at', \Carbon\Carbon::today())->first();

            if ($existingNotification) {
                continue; // Skip if notification already exists for today
            }

            $message = $this->getNotificationMessage($car, $type, $daysUntilExpiry);

            InsuranceExpiryNotification::create([
                'car_id' => $car->id,
                'user_id' => $user->id,
                'notification_type' => $type,
                'status' => 'unread',
                'message' => $message,
                'days_until_expiry' => $daysUntilExpiry,
                'expiry_date' => $car->insurance_expiry_date,
            ]);

            $this->line("Created {$type} notification for {$car->model} to {$user->name}");
        }
    }

    /**
     * Get notification message based on type and days
     */
    private function getNotificationMessage(Car $car, string $type, int $daysUntilExpiry): string
    {
        $carModel = $car->model;
        $days = abs($daysUntilExpiry);
        
        switch ($type) {
            case 'expired':
                return "Insurance for {$carModel} has expired {$days} days ago";
            case 'critical':
                return "Insurance for {$carModel} expires in {$days} days (CRITICAL)";
            case 'warning':
                return "Insurance for {$carModel} expires in {$days} days";
            default:
                return "Insurance notification for {$carModel}";
        }
    }
}
