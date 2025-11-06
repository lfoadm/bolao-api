<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

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
        $request->validate([
            'store_name' => 'required|string|max:100',
            'pix_key' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
        ]);

        $seller = Seller::create([
            'user_id' => $request->user()->id,
            'store_name' => $request->store_name,
            'pix_key' => $request->pix_key,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'message' => 'Seller criado com sucesso!',
            'seller' => $seller
        ], 201);
    }

    /**
     * Exibe um seller específico
     */
    public function show(Seller $seller)
    {
        return response()->json($seller->load('user'));
    }
}
