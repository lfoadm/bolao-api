<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    /**
     * Retorna todos os sellers (útil para admin)
     */
    public function index()
    {
        return response()->json(Seller::with('user')->get());
    }

    /**
     * Cadastra um novo seller (vinculado ao user autenticado)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (Seller::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Você já é um vendedor.'], 400);
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'pix_key' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $seller = Seller::create([
                'user_id' => $user->id,
                'store_name' => $validated['store_name'],
                'pix_key' => $validated['pix_key'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]);

            $user->update([
                'type' => 'seller',
                'pix_key' => $validated['pix_key'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Vendedor criado com sucesso!',
                'seller' => $seller,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar o vendedor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exibe um seller específico
     */
    public function show(Seller $seller)
    {
        return response()->json($seller->load('user'));
    }
}
