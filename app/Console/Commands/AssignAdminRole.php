<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-admin {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign admin role to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        // Find the user
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }
        
        // Check if admin role exists, create if not
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Assign admin role to user
        $user->assignRole($adminRole);
        
        $this->info("Admin role successfully assigned to user: {$user->email} (ID: {$user->id})");
        
        return 0;
    }
} 