<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManipulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manipulations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description');
            $table->bigInteger('plateau_id')->unsigned();
            $table->integer('duration')->unsigned();
            $table->integer('target_slots')->unsigned();
            $table->date('start_date');
            $table->string('location');
            $table->text('available_hours');
            $table->text('requirements');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('plateau_id')->references('id')->on('plateaux')->onDelete('cascade');
        });

        Schema::create('manipulation_user', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('manipulation_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('manipulation_id')->references('id')->on('manipulations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manipulation_user');
        Schema::dropIfExists('manipulations');
    }
}
