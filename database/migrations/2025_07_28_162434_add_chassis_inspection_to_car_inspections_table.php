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
        Schema::table('car_inspections', function (Blueprint $table) {
            if (!Schema::hasColumn('car_inspections', 'chassis_inspection')) {
                $table->text('chassis_inspection')->nullable()->after('car_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_inspections', function (Blueprint $table) {
            if (Schema::hasColumn('car_inspections', 'chassis_inspection')) {
                $table->dropColumn('chassis_inspection');
            }
        });
    }
};
