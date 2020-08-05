<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManipulationStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manipulation_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('manipulation_id')->unsigned();
            $table->unsignedInteger('slot_count');
            $table->unsignedInteger('booking_made');
            $table->unsignedInteger('booking_confirmed');
            $table->unsignedInteger('booking_confirmed_honored');
            $table->unsignedInteger('booking_unconfirmed_honored');

            $table->foreign('manipulation_id')->references('id')->on('manipulations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manipulation_statistics');
    }
}
