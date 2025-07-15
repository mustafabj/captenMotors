<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Car;
use App\Models\CarEquipmentCost;
use App\Models\Notification;
use App\Events\EquipmentCostApprovalRequested;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {--user-id=} {--car-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system by creating sample notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Notification System...');

        // Get or create test data
        $user = $this->getTestUser();
        $car = $this->getTestCar();
        
        if (!$user || !$car) {
            $this->error('Could not find test user or car. Please ensure you have data in the database.');
            return 1;
        }

        $this->info("Using User: {$user->name} (ID: {$user->id})");
        $this->info("Using Car: {$car->model} (ID: {$car->id})");

        // Test 1: Create a direct notification
        $this->info("\n1. Testing direct notification creation...");
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'test_notification',
            'title' => 'Test Notification',
            'message' => 'This is a test notification created at ' . now()->format('Y-m-d H:i:s'),
            'data' => ['test' => true]
        ]);

        $this->info("✓ Created notification ID: {$notification->id}");

        // Test 2: Create an equipment cost and trigger approval request
        $this->info("\n2. Testing equipment cost approval request...");
        
        $cost = CarEquipmentCost::create([
            'car_id' => $car->id,
            'user_id' => $user->id,
            'description' => 'Test Equipment Cost',
            'amount' => 150.00,
            'cost_date' => now(),
            'status' => 'pending'
        ]);

        $this->info("✓ Created equipment cost ID: {$cost->id}");

        // Test 3: Trigger the event (this would normally happen when a non-admin adds a cost)
        $this->info("\n3. Testing event dispatch...");
        
        try {
            event(new EquipmentCostApprovalRequested($car, $cost, $user));
            $this->info("✓ Event dispatched successfully");
        } catch (\Exception $e) {
            $this->error("✗ Event dispatch failed: " . $e->getMessage());
        }

        // Test 4: Check notification count
        $this->info("\n4. Checking notification counts...");
        $totalNotifications = $user->notifications()->count();
        $unreadNotifications = $user->unreadNotifications()->count();
        
        $this->info("✓ Total notifications: {$totalNotifications}");
        $this->info("✓ Unread notifications: {$unreadNotifications}");

        // Test 5: Test admin functionality
        $this->info("\n5. Testing admin functionality...");
        $adminUsers = User::getAdmins();
        $this->info("✓ Found " . $adminUsers->count() . " admin users");

        foreach ($adminUsers as $admin) {
            $this->info("  - Admin: {$admin->name} (ID: {$admin->id})");
        }

        // Test 6: Test equipment cost status methods
        $this->info("\n6. Testing equipment cost status methods...");
        $this->info("✓ Cost status: " . $cost->getStatusText());
        $this->info("✓ Is pending: " . ($cost->isPending() ? 'Yes' : 'No'));
        $this->info("✓ Badge class: " . $cost->getStatusBadgeClass());

        $this->info("\n✅ Notification system test completed successfully!");
        $this->info("\nTo test the full system:");
        $this->info("1. Visit a car show page");
        $this->info("2. Add an equipment cost as a non-admin user");
        $this->info("3. Check the notification drawer in the header");
        $this->info("4. As an admin, approve/reject the cost request");

        return 0;
    }

    private function getTestUser()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            return User::find($userId);
        }

        return User::first();
    }

    private function getTestCar()
    {
        $carId = $this->option('car-id');
        
        if ($carId) {
            return Car::find($carId);
        }

        return Car::first();
    }
}
