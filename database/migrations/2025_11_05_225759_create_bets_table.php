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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pool_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Palpite do usuário
            $table->unsignedTinyInteger('bet_team_a');
            $table->unsignedTinyInteger('bet_team_b');

            // Status e controle
            $table->boolean('is_winner')->default(false);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('winner_value', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'progress', 'lost'])->default('pending');
            $table->boolean('is_prize_claimed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
