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
            // Add 10 body inspection fields only if they don't exist
            if (!Schema::hasColumn('car_inspections', 'hood')) {
                $table->enum('hood', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('body_notes');
            }
            if (!Schema::hasColumn('car_inspections', 'front_right_fender')) {
                $table->enum('front_right_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('hood');
            }
            if (!Schema::hasColumn('car_inspections', 'front_left_fender')) {
                $table->enum('front_left_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('front_right_fender');
            }
            if (!Schema::hasColumn('car_inspections', 'rear_right_fender')) {
                $table->enum('rear_right_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('front_left_fender');
            }
            if (!Schema::hasColumn('car_inspections', 'rear_left_fender')) {
                $table->enum('rear_left_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('rear_right_fender');
            }
            if (!Schema::hasColumn('car_inspections', 'trunk_door')) {
                $table->enum('trunk_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('rear_left_fender');
            }
            if (!Schema::hasColumn('car_inspections', 'front_right_door')) {
                $table->enum('front_right_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('trunk_door');
            }
            if (!Schema::hasColumn('car_inspections', 'rear_right_door')) {
                $table->enum('rear_right_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('front_right_door');
            }
            if (!Schema::hasColumn('car_inspections', 'front_left_door')) {
                $table->enum('front_left_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('rear_right_door');
            }
            if (!Schema::hasColumn('car_inspections', 'rear_left_door')) {
                $table->enum('rear_left_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->nullable()->after('front_left_door');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_inspections', function (Blueprint $table) {
            $columns = [
                'hood',
                'front_right_fender',
                'front_left_fender',
                'rear_right_fender',
                'rear_left_fender',
                'trunk_door',
                'front_right_door',
                'rear_right_door',
                'front_left_door',
                'rear_left_door'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('car_inspections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
