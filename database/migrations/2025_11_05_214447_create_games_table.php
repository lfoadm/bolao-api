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
        Schema::create('games', function (Blueprint $table) {
            $table->id();

            $table->string('league');
            $table->string('team_a');
            $table->string('team_b');

            $table->dateTime('match_datetime');

            // Após finalização oficial do jogo
            $table->unsignedInteger('final_score_a')->nullable();
            $table->unsignedInteger('final_score_b')->nullable();
            $table->boolean('is_finished')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
