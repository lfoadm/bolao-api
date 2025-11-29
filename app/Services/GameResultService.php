<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GameResultService
{
    /**
     * Finaliza jogo, pools e premia os ganhadores.
     */
    public function finalizeGame(Game $game, int $scoreA, int $scoreB, $user)
    {
        // ---------- 1. Validar permissões ----------
        if ($user->type !== 'admin') {
            abort(403, 'Apenas administradores podem finalizar jogos.');
        }

        // ---------- 2. Validar horário ----------
        $allowedTime = Carbon::parse($game->match_datetime)->addHours(2);

        if (now()->lt($allowedTime)) {
            abort(403, "O jogo só pode ser finalizado 2 horas após o início.");
        }

        // ---------- 3. Evitar reprocessamento ----------
        if ($game->is_finished) {
            abort(400, 'Este jogo já está finalizado.');
        }

        // ---------- 4. Transação segura ----------
        return DB::transaction(function () use ($game, $scoreA, $scoreB) {

            // 4.1 Finaliza o jogo
            $game->update([
                'final_score_a' => $scoreA,
                'final_score_b' => $scoreB,
                'is_finished'   => true,
            ]);

            // 4.2 Buscar pools vinculados
            $pools = Pool::where('game_id', $game->id)
                ->where('status', '!=', 'finished')
                ->get();

            foreach ($pools as $pool) {

                // Total de apostas
                $totalBets = $pool->bets()->count();

                // Bets vencedoras
                $winners = $pool->bets()
                    ->where('bet_team_a', $scoreA)
                    ->where('bet_team_b', $scoreB)
                    ->get();

                $winnerCount = $winners->count();

                // Atualiza pool como finalizado
                $pool->update([
                    'status' => 'finished',
                    'winner_count' => $winnerCount,
                    'total_bets' => $totalBets,
                ]);

                // ---------- 4.3 Premiação ----------
                if ($winnerCount > 0) {

                    $valuePerWinner = $pool->current_prize / $winnerCount;

                    foreach ($winners as $bet) {
                        $bet->update([
                            'is_winner' => true,
                            'winner_value' => $valuePerWinner,
                        ]);
                    }
                }
            }

            return true;
        });
    }
}
