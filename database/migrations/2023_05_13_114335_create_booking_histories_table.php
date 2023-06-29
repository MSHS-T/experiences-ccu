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

        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id();
            $table->string('hashed_email');
            $table->date('birthday');
            $table->unsignedInteger('booking_made');
            $table->unsignedInteger('booking_confirmed');
            $table->unsignedInteger('booking_confirmed_honored');
            $table->unsignedInteger('booking_unconfirmed_honored');
            $table->boolean('blocked');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
    }
};
