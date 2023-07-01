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
        Schema::create('attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plateau_id')->constrained('plateaux');
            $table->foreignId('manipulation_manager_id')->constrained('users');
            $table->foreignId('creator_id')->constrained('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('allowed_halfdays');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributions');
    }
};
