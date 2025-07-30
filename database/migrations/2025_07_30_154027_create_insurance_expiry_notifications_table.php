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
        Schema::create('insurance_expiry_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('notification_type', ['expired', 'critical', 'warning'])->default('warning');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->text('message');
            $table->integer('days_until_expiry');
            $table->date('expiry_date');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status'], 'ien_user_status_idx');
            $table->index(['car_id', 'notification_type'], 'ien_car_type_idx');
            $table->index('created_at', 'ien_created_at_idx');
            $table->index('expiry_date', 'ien_expiry_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_expiry_notifications');
    }
};
