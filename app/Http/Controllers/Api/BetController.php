<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BetController extends Controller
{
    /**
     * Criar uma aposta em um bolão (Consumer autenticado)
     */
    public function store(Request $request, Pool $pool)
    {
        $request->validate([
            'bet_team_a' => 'required|integer|min:0|max:20',
            'bet_team_b' => 'required|integer|min:0|max:20',
        ]);

        // Verifica se o bolão ainda está aberto
        if ($pool->status !== 'open' || now()->greaterThan($pool->deadline)) {
            return response()->json(['message' => 'Apostas encerradas para este bolão.'], 403);
        }

        // Impede o usuário de apostar duas vezes no mesmo bolão
        // VOU PERMITIR APOSTAR VÁRIAS VEZES NO MESMO BOLAO
        // $alreadyBet = Bet::where('pool_id', $pool->id)
        //     ->where('user_id', $request->user()->id)
        //     ->exists();

        // if ($alreadyBet) {
        //     return response()->json(['message' => 'Você já apostou neste bolão.'], 409);
        // }

        $bet = Bet::create([
            'pool_id' => $pool->id,
            'user_id' => $request->user()->id,
            'bet_team_a' => $request->bet_team_a,
            'bet_team_b' => $request->bet_team_b,
            'amount_paid' => $pool->entry_value,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Aposta registrada com sucesso!',
            'bet' => $bet
        ], 201);
    }

    /**
     * Exibir todas as apostas do usuário logado
     */
    public function myBets(Request $request)
    {
        $bets = Bet::with('pool.seller.user')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($bets);
    }
}
