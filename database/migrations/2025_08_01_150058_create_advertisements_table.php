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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('expiration_date');
            $table->decimal('offer_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->enum('status', ['active', 'expired', 'sold', 'cancelled'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'expiration_date']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
