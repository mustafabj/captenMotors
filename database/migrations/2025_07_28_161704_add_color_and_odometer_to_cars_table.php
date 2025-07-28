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
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'color')) {
                $table->string('color')->nullable()->after('model');
            }
            if (!Schema::hasColumn('cars', 'odometer')) {
                $table->integer('odometer')->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('cars', 'odometer')) {
                $table->dropColumn('odometer');
            }
        });
    }
};
