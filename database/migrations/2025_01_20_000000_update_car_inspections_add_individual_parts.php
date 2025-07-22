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
            // Remove the single chassis inspection field
            // $table->dropColumn('chassis_inspection');
            
            // Add individual fields for each car part
            $table->enum('hood', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('car_id');
            $table->enum('front_right_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('hood');
            $table->enum('front_left_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('front_right_fender');
            $table->enum('rear_right_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('front_left_fender');
            $table->enum('rear_left_fender', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('rear_right_fender');
            $table->enum('trunk_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('rear_left_fender');
            $table->enum('front_right_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('trunk_door');
            $table->enum('rear_right_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('front_right_door');
            $table->enum('front_left_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('rear_right_door');
            $table->enum('rear_left_door', ['clean_and_free_of_filler', 'painted', 'fully_repainted'])->default('clean_and_free_of_filler')->after('front_left_door');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_inspections', function (Blueprint $table) {
            // Remove the individual part fields
            $table->dropColumn([
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
            ]);
            
            // Add back the single chassis inspection field
            $table->text('chassis_inspection')->nullable()->after('car_id');
        });
    }
}; 