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
        Schema::table('manipulation_statistics', function (Blueprint $table) {
            $table->unsignedInteger('half_day_count')->default(0)->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manipulation_statistics', function (Blueprint $table) {
            $table->dropColumn('half_day_count');
        });
    }
};
