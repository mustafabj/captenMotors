<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Remove chassis_number and engine_type columns
            $table->dropColumn(['chassis_number', 'engine_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Add back the columns
            $table->string('chassis_number')->unique()->after('plate_number');
            $table->string('engine_type', 50)->nullable()->after('engine_capacity');
        });
    }
}; 