<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pool;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PoolController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pools = Pool::with(['seller.user'])
        ->withCount('bets') // adiciona bets_count automaticamente
        ->where('is_active', true)
        ->whereHas('seller', fn($q) => $q->where('user_id', $user->id))
        ->orderBy('deadline', 'asc')
        ->get();

        return response()->json($pools);
    }

    /**
     * Criar um novo bolão (apenas seller autenticado)
     */
    public function store(Request $request)
    {
        // Validação
        $validated = $request->validate([
            'theme' => 'required|in:futebol,futsal',
            'team_a' => 'required|string|max:50',
            'team_b' => 'required|string|max:50',
            'match_date' => 'required|date|after:now',
            'title' => 'required|string|max:100',
            'rules' => 'nullable|string',
            'commission' => 'nullable|numeric|min:0|max:100',
            'entry_value' => 'required|numeric|min:1',
            'image' => 'nullable|string',
        ]);

        // Obter seller autenticado
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->firstOrFail();

        // Calcular deadline: 15 min antes da partida
        $matchDate = Carbon::parse($validated['match_date']);
        $deadline = $matchDate->copy()->subMinutes(15);

        // Definir imagem padrão conforme o tema
        $image = match ($validated['theme']) {
            'futebol' => 'futebol.jpg',
            'futsal' => 'futsal.jpg',
            default => 'default.jpg',
        };

        // Criar Pool
        $pool = Pool::create([
            'seller_id' => $seller->id,
            'theme' => $validated['theme'],
            'team_a' => $validated['team_a'],
            'team_b' => $validated['team_b'],
            'match_date' => $matchDate,
            'deadline' => $deadline,
            'title' => $validated['title'],
            'rules' => $validated['rules'] ?? null,
            'commission' => $validated['commission'] ?? 10,
            'entry_value' => $validated['entry_value'],
            'image' => $image,
        ]);

        return response()->json([
            'message' => 'Bolão criado com sucesso!',
            'pool' => $pool,
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
