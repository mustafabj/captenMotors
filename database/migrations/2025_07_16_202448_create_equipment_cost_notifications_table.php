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
        Schema::create('equipment_cost_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_equipment_cost_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('notified_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('notification_type', ['approval_requested', 'approved', 'rejected', 'transferred']);
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->text('message');
            $table->json('additional_data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['notified_user_id', 'status'], 'ecn_user_status_idx');
            $table->index(['car_equipment_cost_id', 'notification_type'], 'ecn_cost_type_idx');
            $table->index('created_at', 'ecn_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_cost_notifications');
    }
};
