<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('crypted_email')->unique();
            $table->unsignedInteger('booking_made');
            $table->unsignedInteger('booking_confirmed');
            $table->unsignedInteger('booking_confirmed_honored');
            $table->unsignedInteger('booking_unconfirmed_honored');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_history');
    }
}
