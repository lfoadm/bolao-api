<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\GameResultService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::where('match_datetime', '>', now())->orderBy('league', 'asc')->get();
        return GameResource::collection($games);
    }

    public function getGames()
    {
        $gamesAll = Game::query()
            ->withCount('pools') // quantidade de bolões
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($game) {

                // STATUS DO JOGO
                $matchDate = $game->match_datetime;

                $game->status = match (true) {
                    $game->is_finished === true => 'finished',
                    $matchDate->isPast()        => 'closed',
                    default                     => 'open',
                };

                return $game;
            });

        return response()->json($gamesAll);
    }

    public function store(Request $request)
    {
        // Espera um array de jogos, onde a chave principal é um array de objetos.
        $gamesData = $request->all();

        if (!is_array($gamesData)) {
            // Retorna um erro se o corpo da requisição não for um array
            return response()->json(['message' => 'O corpo da requisição deve ser um array de jogos.'], 422);
        }
        
        $createdGames = collect();

        foreach ($gamesData as $gameData) {
            // Validação específica para CADA jogo dentro do array
            $validated = validator($gameData, [
                'team_a'         => 'required|string|max:100',
                'team_b'         => 'required|string|max:100',
                'league'         => 'required|string|max:100',
                'match_datetime' => 'required|date|after:now',
                'final_score_a'  => 'nullable|integer|min:0',
                'final_score_b'  => 'nullable|integer|min:0',
            ])->validate();

            // Cria o jogo
            $game = Game::create([
                'team_a'         => $validated['team_a'],
                'team_b'         => $validated['team_b'],
                'league'         => $validated['league'],
                'match_datetime' => $validated['match_datetime'],
                'final_score_a'  => $validated['final_score_a'] ?? null,
                'final_score_b'  => $validated['final_score_b'] ?? null,
                'is_finished'    => false,
            ]);

            $createdGames->push($game);
        }

        // Retorna a coleção de jogos criados
        return GameResource::collection($createdGames);
    }

    public function finalize(Request $request, Game $game, GameResultService $service)
    {
        $validated = $request->validate([
            'final_score_a' => 'required|integer|min:0',
            'final_score_b' => 'required|integer|min:0',
        ]);

        $service->finalizeGame(
            $game,
            $validated['final_score_a'],
            $validated['final_score_b'],
            $request->user()
        );

        return response()->json(['message' => 'Jogo finalizado e pools processados com sucesso.']);
    }

    public function show(Game $game)
    {
        $game->load([
            'pools',              // se quiser mostrar pools vinculados
            'pools.bets',         // apostas dentro dos pools
            'pools.bets.user',    // usuários das apostas
        ]);

        return response()->json($game);
    }

}
