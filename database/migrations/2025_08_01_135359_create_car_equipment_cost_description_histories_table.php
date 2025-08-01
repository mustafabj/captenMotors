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
        Schema::create('car_equipment_cost_description_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_equipment_cost_id');
            $table->text('old_description');
            $table->text('new_description');
            $table->unsignedBigInteger('changed_by_user_id');
            $table->text('change_reason')->nullable();
            $table->timestamps();
            
            // Add custom foreign key names to avoid MySQL identifier length limit
            $table->foreign('car_equipment_cost_id', 'fk_desc_hist_cost_id')->references('id')->on('car_equipment_costs')->onDelete('cascade');
            $table->foreign('changed_by_user_id', 'fk_desc_hist_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_equipment_cost_description_histories');
    }
};
