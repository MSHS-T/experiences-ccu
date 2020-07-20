<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('slot_id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->dateTime('created_at');
            $table->boolean('confirmed')->nullable(true)->default(null);
            $table->string('confirmation_code')->nullable(true)->default(null);
            $table->dateTime('confirm_before')->nullable(true)->default(null);
            $table->boolean('honored')->nullable(true)->default(null);

            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('cascade');
        });

        Schema::table('manipulations', function (Blueprint $table) {
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manipulations', function (Blueprint $table) {
            $table->dropForeign('manipulations_booking_id_foreign');
            $table->dropColumn('booking_id');
        });

        Schema::dropIfExists('bookings');
    }
}
