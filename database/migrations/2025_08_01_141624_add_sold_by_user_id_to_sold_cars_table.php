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
        Schema::table('sold_cars', function (Blueprint $table) {
            $table->foreignId('sold_by_user_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sold_cars', function (Blueprint $table) {
            try {
                $table->dropForeign(['sold_by_user_id']);
            } catch (\Exception $e) {
                // Foreign key doesn't exist, continue
            }
            try {
                $table->dropColumn('sold_by_user_id');
            } catch (\Exception $e) {
                // Column doesn't exist, continue
            }
        });
    }
};
