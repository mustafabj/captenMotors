<?php

// Simple script to assign admin role to user 1
// Run this with: php assign_admin_role.php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

try {
    // Find user 1
    $user = User::find(1);
    
    if (!$user) {
        echo "Error: User with ID 1 not found!\n";
        exit(1);
    }
    
    echo "Found user: {$user->email} (ID: {$user->id})\n";
    
    // Check if admin role exists, create if not
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    echo "Admin role exists/created\n";
    
    // Assign admin role to user
    $user->assignRole($adminRole);
    
    echo "Success: Admin role assigned to user: {$user->email} (ID: {$user->id})\n";
    
    // Verify the role was assigned
    if ($user->hasRole('admin')) {
        echo "Verification: User {$user->email} now has admin role\n";
    } else {
        echo "Warning: Role assignment verification failed\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 