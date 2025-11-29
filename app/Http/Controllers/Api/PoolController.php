<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Game;
use App\Models\Pool;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PoolController extends Controller
{
    /**
     * Listar todos os bolões do seller autenticado
     */
    public function index(Request $request)
    {
        // Garantir que o usuário tem seller vinculado
        $seller = Seller::where('user_id', $request->user()->id)->first();

        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum vendedor encontrado para este usuário.',
                'data' => []
            ], 404);
        }

        // Buscar bolões do seller
        $pools = Pool::with([
                'game:id,team_a,team_b,match_datetime,is_finished,final_score_a,final_score_b',
                'seller:id,user_id'
            ])
            ->withCount('bets')
            ->where('seller_id', $seller->id)
            ->orderByDesc('created_at')
            // OU → mais recomendado: ordenar pela data do jogo
            // ->orderBy(Game::select('match_datetime')->whereColumn('games.id', 'pools.game_id'))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lista de bolões carregada com sucesso.',
            'data' => $pools
        ]);
    }


    /**
     * Criar um bolão baseado em um GAME já cadastrado
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id'             => 'required|exists:games,id',
            'entry_value'         => 'required|numeric|min:1',
            // 'commission'          => 'nullable|integer|min:0|max:8',
            'title'               => 'required|string|max:100',
            'rules'               => 'nullable|string',
        ]);

        // seller autenticado
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        // busca game
        $game = Game::findOrFail($validated['game_id']);

        // busca taxa global da plataforma
        $taxa = Fee::where('is_active')->first();
        $platformFee = $taxa ? $taxa->platform_fee_percent : 5; // padrão 5%

        // cria o pool
        $pool = Pool::create([
            'seller_id'          => $seller->id,
            'game_id'            => $game->id,
            'title'              => $validated['title'],
            'rules'              => $validated['rules'] ?? null,
            'entry_value'        => $validated['entry_value'],
            'commission'         => 8,
            'platform_fee'       => $platformFee, // sempre percentual (ex: 5 = 5%)
            'status'             => 'open',
        ]);

        return response()->json([
            'message' => 'Bolão criado com sucesso!',
            'pool'    => $pool->load('game', 'seller'),
        ], 201);
    }

    /**
     * Exibir um bolão específico
     */
    public function show(Pool $pool)
    {
        return response()->json(
            $pool->load('game', 'seller', 'bets.user')
        );
    }

    /**
     * Fechar manualmente um bolão (opcional)
     */
    public function close(Pool $pool)
    {
        if ($pool->status !== 'open') {
            return response()->json([
                'message' => 'Este bolão já está fechado ou finalizado.'
            ], 400);
        }

        $pool->update(['status' => 'closed']);

        return response()->json([
            'message' => 'Bolão fechado com sucesso.',
            'pool' => $pool
        ]);
    }

    public function destroy($id)
    {
        // Busca o pool
        $pool = Pool::withCount('bets')->find($id);

        if (!$pool) {
            return response()->json([
                'message' => 'Bolão não encontrado.'
            ], 404);
        }

        // Verifica se já existem apostas
        if ($pool->bets_count > 0) {
            return response()->json([
                'message' => 'NÃO SEJA MALANDRO! Esse bolão já tem apostas e não pode ser excluído.'
            ], 400);
        }

        // Pode excluir
        $pool->delete();

        return response()->json([
            'message' => 'Bolão excluído com sucesso.'
        ], 200);
    }

}
