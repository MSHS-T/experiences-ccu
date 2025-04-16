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
        Schema::table('manipulations', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_booking_per_slot')->default(1)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manipulations', function (Blueprint $table) {
            $table->dropColumn('max_booking_per_slot');
        });
    }
};
