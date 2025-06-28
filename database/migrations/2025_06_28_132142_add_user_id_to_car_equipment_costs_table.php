<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column already exists
        if (!Schema::hasColumn('car_equipment_costs', 'user_id')) {
            Schema::table('car_equipment_costs', function (Blueprint $table) {
                // Add the column as nullable first
                $table->foreignId('user_id')->after('car_id')->nullable();
            });
        }

        // Get the first user ID or create a default one
        $defaultUserId = DB::table('users')->first()?->id ?? 1;

        // Update existing records with the default user ID
        DB::table('car_equipment_costs')->whereNull('user_id')->update(['user_id' => $defaultUserId]);

        // Now make the column non-nullable
        Schema::table('car_equipment_costs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        // Add foreign key constraint (this will fail if it already exists, but that's okay)
        try {
            Schema::table('car_equipment_costs', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign key constraint already exists, ignore the error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_equipment_costs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
