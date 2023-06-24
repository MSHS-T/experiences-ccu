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

        Schema::create('manipulations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId('plateau_id')->constrained('plateaux');
            $table->unsignedInteger('duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location');
            $table->json('available_hours');
            $table->json('requirements');
            $table->boolean('published')->default(false);
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manipulations');
    }
};
