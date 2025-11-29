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
            // $table->id();
            // $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            // $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            // // Informações internas do bolão
            // $table->decimal('entry_value', 10, 2)->default(0);
            // $table->decimal('commission', 10, 2)->default(0); // calculado
            // $table->decimal('platform_fee', 10, 2)->default(0); // buscado da tabela taxas
            
            // // Dados gerais do bolão
            // $table->string('title')->comment('Nome da partida');
            // $table->text('rules')->nullable()->comment('Regras individuais do seller');
            // $table->enum('status', ['open', 'closed', 'finished'])->default('open');
            // $table->timestamps();

            $table->id();

            // Relacionamentos
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');

            // Finanças
            $table->decimal('entry_value', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(8); // calculado
            $table->decimal('platform_fee', 10, 2)->default(0); // da tabela taxas

            // Informações do bolão
            $table->string('title')->comment('Nome da partida');
            $table->text('rules')->nullable()->comment('Regras individuais do seller');
            $table->enum('status', ['open', 'closed', 'finished'])->default('open');

            // Estatísticas após finalizar o jogo
            $table->unsignedInteger('winner_count')->nullable()->comment('Quantos apostadores acertaram o placar');
            $table->unsignedInteger('total_bets')->nullable()->comment('Quantidade total de apostas no pool');

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
