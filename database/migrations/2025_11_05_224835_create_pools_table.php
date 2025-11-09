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
            $table->enum('theme', ['futebol', 'futsal'])->nullable();
            $table->string('team_a');
            $table->string('team_b');
            $table->dateTime('match_date')->nullable()->comment('Data e hora da partida');
            $table->dateTime('deadline')->nullable()->comment('Data e hora limite para apostar');

            // Resultado oficial (inserido após o jogo)
            $table->unsignedTinyInteger('score_team_a')->nullable();
            $table->unsignedTinyInteger('score_team_b')->nullable();

            // Dados gerais do bolão
            $table->string('image')->nullable();
            $table->string('title')->comment('Nome da partida');
            $table->text('rules')->nullable()->comment('Regras individuais do seller');
            $table->decimal('commission', 5, 2)->default(10)->comment('Percentual de comissão do seller');
            $table->decimal('entry_value', 10, 2)->comment('Valor da aposta');
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
