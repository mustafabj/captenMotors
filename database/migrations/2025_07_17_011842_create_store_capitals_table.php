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
        Schema::create('store_capitals', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2); // Positive for addition, negative for withdrawal
            $table->string('description')->nullable(); // Source or reason for the transaction
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_capitals');
    }
};
