<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('manipulation_id')->unsigned();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('subject_first_name')->nullable(true)->default(null);
            $table->string('subject_last_name')->nullable(true)->default(null);
            $table->string('subject_email')->nullable(true)->default(null);
            $table->boolean('subject_confirmed')->nullable(true)->default(null);
            $table->string('subject_confirmation_code')->nullable(true)->default(null);

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
        Schema::dropIfExists('slots');
    }
}
