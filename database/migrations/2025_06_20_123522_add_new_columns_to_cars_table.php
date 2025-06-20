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
            $table->string('vehicle_category')->nullable()->after('model');
            $table->string('engine_type')->nullable()->after('engine_capacity');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('purchase_date');
            $table->string('place_of_manufacture')->nullable()->after('manufacturing_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['vehicle_category', 'engine_type', 'purchase_price', 'place_of_manufacture']);
        });
    }
};
