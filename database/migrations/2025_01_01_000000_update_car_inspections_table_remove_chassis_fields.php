<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::table('car_inspections', function (Blueprint $table) {
    //         // Remove the four separate chassis fields
    //         $table->dropColumn([
    //             'front_chassis_right',
    //             'front_chassis_left',
    //             'rear_chassis_right',
    //             'rear_chassis_left'
    //         ]);
            
    //         // Add the new single chassis inspection field
    //         $table->text('chassis_inspection')->nullable()->after('car_id');
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     Schema::table('car_inspections', function (Blueprint $table) {
    //         // Remove the new field
    //         $table->dropColumn('chassis_inspection');
            
    //         // Add back the four separate chassis fields
    //         $table->string('front_chassis_right')->after('car_id');
    //         $table->string('front_chassis_left')->after('front_chassis_right');
    //         $table->string('rear_chassis_right')->after('front_chassis_left');
    //         $table->string('rear_chassis_left')->after('rear_chassis_right');
    //     });
    // }
}; 