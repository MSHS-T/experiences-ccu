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
        Schema::disableForeignKeyConstraints();

        Schema::create('manipulation_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('slot_count');
            $table->unsignedInteger('booking_made');
            $table->unsignedInteger('booking_confirmed');
            $table->unsignedInteger('booking_confirmed_honored');
            $table->unsignedInteger('booking_unconfirmed_honored');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manipulation_statistics');
    }
};
