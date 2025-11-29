<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BetResource;
use App\Models\Bet;
use App\Models\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BetController extends Controller
{
    /**
     * Criar aposta e gerar código PIX
     */
    public function store(Request $request, Pool $pool)
    {
        // 1. Validação dos dados enviados
        $request->validate([
            'bet_team_a' => 'required|integer|min:0',
            'bet_team_b' => 'required|integer|min:0',
        ]);

        // 2. Verificação de deadline configurada
        if (empty($pool->deadline)) {
            return response()->json([
                'message' => 'Este bolão está com horário inválido. Contate o administrador.'
            ], 500);
        }

        // 3. Verifica se o bolão ainda está aberto
        $deadline = Carbon::parse($pool->deadline);

        if ($pool->status !== 'open' || now()->greaterThan($deadline)) {
            return response()->json([
                'message' => 'Apostas encerradas para este bolão.'
            ], 403);
        }

        // 4. Gerar código PIX (mock)
        $pixCode = 'PIX-' . strtoupper(uniqid());

        // 5. Criar aposta
        $bet = Bet::create([
            'pool_id'      => $pool->id,
            'user_id'      => $request->user()->id,
            'bet_team_a'   => $request->bet_team_a,
            'bet_team_b'   => $request->bet_team_b,
            'amount_paid'  => $pool->entry_value,
            'status'       => 'pending',
            'pix_code'     => $pixCode,
        ]);

        return response()->json([
            "success"   => true,
            "bet_id"    => $bet->id,
            "pix_code"  => $pixCode,
            "status"    => $bet->status,
        ]);
    }

    /**
     * Consultar status da aposta
     */
    public function show($id, Request $request)
    {
        $bet = Bet::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('pool')
            ->firstOrFail();

        return response()->json([
            'bet_id'      => $bet->id,
            'pool_id'     => $bet->pool_id,
            'status'      => $bet->status,
            'amount_paid' => $bet->amount_paid,
            'created_at'  => $bet->created_at,
        ]);
    }

    /**
     * Busca as apostas do usuário atual com seus relacionamentos essenciais.
     * * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myBets(Request $request)
    {
        $bets = Bet::with('pool.game')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return BetResource::collection($bets);
    }
}