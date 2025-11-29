<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            "bet_id" => "required|integer|exists:bets,id",
            "status" => "required|string|in:paid,expired,failed",
        ]);

        $bet = Bet::find($request->bet_id);

        $bet->status = $request->status;
        $bet->save();

        return response()->json([
            "success" => true,
            "message" => "Status da aposta atualizado.",
            "bet"     => $bet,
        ]);
    }
}
