<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoolController extends Controller
{
    public function index()
    {
        return response()->json(
            Pool::with('seller.user')
                ->where('is_active', true)
                ->where('status', 'open')
                ->orderBy('deadline', 'asc')
                ->get()
        );
    }

    /**
     * Criar um novo bolão (apenas seller autenticado)
     */
    public function store(Request $request)
    {
        $request->validate([
            'team_a' => 'required|string|max:50',
            'team_b' => 'required|string|max:50',
            'match_date' => 'required|date',
            'deadline' => 'required|date|before:match_date',
            'title' => 'required|string|max:100',
            'rules' => 'nullable|string',
            'commission' => 'nullable|numeric|min:0|max:100',
            'entry_value' => 'required|numeric|min:1',
        ]);

        // Buscar o seller vinculado ao user logado
        $seller = Seller::where('user_id', $request->user()->id)->firstOrFail();

        $pool = Pool::create([
            'seller_id' => $seller->id,
            'team_a' => $request->team_a,
            'team_b' => $request->team_b,
            'match_date' => $request->match_date,
            'deadline' => $request->deadline,
            'title' => $request->title,
            'rules' => $request->rules,
            'commission' => $request->commission ?? 10,
            'entry_value' => $request->entry_value,
        ]);

        return response()->json([
            'message' => 'Bolão criado com sucesso!',
            'pool' => $pool
        ], 201);
    }

    /**
     * Atualizar resultado do jogo e definir vencedores
     */
    public function update(Request $request, Pool $pool)
    {
        $request->validate([
            'score_team_a' => 'required|integer|min:0',
            'score_team_b' => 'required|integer|min:0',
        ]);

        $pool->update([
            'score_team_a' => $request->score_team_a,
            'score_team_b' => $request->score_team_b,
            'status' => 'finished',
            'is_active' => false,
        ]);

        // Verificar vencedores
        $pool->checkWinners();

        return response()->json([
            'message' => 'Resultado atualizado e vencedores definidos!',
            'pool' => $pool->load('bets.user'),
        ]);
    }

    /**
     * Exibir detalhes do bolão (com apostas)
     */
    public function show(Pool $pool)
    {
        return response()->json($pool->load('seller.user', 'bets.user'));
    }
}
