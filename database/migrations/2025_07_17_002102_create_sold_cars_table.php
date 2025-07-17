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
        Schema::create('sold_cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->decimal('sale_price', 12, 2);
            $table->enum('payment_method', ['cash', 'check', 'separated']);
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('remaining_amount', 12, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sold_cars');
    }
};
