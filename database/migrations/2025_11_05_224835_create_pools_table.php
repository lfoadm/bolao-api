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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
        
            // Jogo
            $table->string('team_a');
            $table->string('team_b');
            $table->dateTime('match_date')->nullable()->comment('data e hora da partida');
            $table->dateTime('deadline')->comment('data e hora limite aposta');// até quando pode apostar

            // Resultado oficial (inserido após o jogo)
            $table->unsignedTinyInteger('score_team_a')->nullable();
            $table->unsignedTinyInteger('score_team_b')->nullable();

            // Dados gerais do bolão
            $table->string('title')->comment('nome da partida');
            $table->text('rules')->nullable()->comment('regras individuais do SELLER');
            $table->decimal('commission', 5, 2)->default(10)->comment('valor de comissão do seller, restante é premiação'); // %
            $table->decimal('entry_value', 10, 2)->comment('valor da aposta');
            $table->enum('status', ['open', 'closed', 'finished'])->default('open');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pools');
    }
};
