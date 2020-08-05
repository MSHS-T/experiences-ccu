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

        Schema::table('slots', function (Blueprint $table) {
            $table->dropColumn('subject_first_name');
            $table->dropColumn('subject_last_name');
            $table->dropColumn('subject_email');
            $table->dropColumn('subject_confirmed');
            $table->dropColumn('subject_confirmation_code');
            $table->dropColumn('subject_confirm_before');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->string('subject_first_name');
            $table->string('subject_last_name');
            $table->string('subject_email');
            $table->boolean('subject_confirmed');
            $table->string('subject_confirmation_code');
            $table->dateTime('subject_confirm_before');
        });

        Schema::dropIfExists('bookings');
    }
}
