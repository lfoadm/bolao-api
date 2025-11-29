<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Pool;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $pools = Pool::withCount('bets')
            ->with(['seller', 'game'])
            ->whereHas('game', fn ($q) =>
                $q->where('match_datetime', '>', now()) // Apenas jogos futuros
            )
            ->orderBy(
                Game::select('match_datetime')
                    ->whereColumn('games.id', 'pools.game_id')
                    ->limit(1)
            )
            ->get()
            ->map(function ($pool) {

                $game = $pool->game;

                // Fechamento automático: 10 min antes da partida
                $deadline = $game->match_datetime->copy()->subMinutes(10);

                $status = match (true) {
                    $pool->status === 'finished' => 'finished',
                    now()->greaterThanOrEqualTo($deadline) => 'closed',
                    default => 'open',
                };

                return [
                    'id' => $pool->id,
                    'title' => $pool->title,
                    'sport' => 'Futebol',
                    'image' => '/assets/images/futebol.jpeg',
                    'store_name' => $pool->seller->store_name,
                    'entry_value' => number_format($pool->entry_value, 2, ',', '.'),

                    // Dados do jogo
                    'match_datetime' => $game->match_datetime->toIso8601String(),
                    'team_a' => $game->team_a,
                    'team_b' => $game->team_b,

                    // Premiação calculada via accessor (model Pool)
                    'prize' => 'R$ ' . number_format($pool->current_prize, 2, ',', '.'),

                    // Status atualizado
                    'status' => $status,
                ];
            });

        return response()->json($pools);
    }

}
