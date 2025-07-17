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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->year('manufacturing_year');
            $table->integer('number_of_keys');
            $table->string('chassis_number')->unique();
            $table->string('plate_number')->nullable()->unique();
            $table->string('engine_capacity');
            $table->date('purchase_date');
            $table->date('insurance_expiry_date');
            $table->decimal('expected_sale_price', 10, 2);
            $table->enum('status', [
                'not_received',
                'paint',
                'upholstery',
                'mechanic',
                'electrical',
                'agency',
                'polish',
                'ready',
                'sold'
            ])->default('not_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
