<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Simula envio OTP (futuro: integrar Twilio, UltraMsg, etc.)
    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required', 'name' => 'required']);
        // $otp = rand(100000, 999999);
        $otp = 123;

        $user = User::updateOrCreate(
            ['phone' => $request->phone],
            [
                'name' => $request->name,
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5),
            ]
        );

        // Aqui enviaria o OTP por WhatsApp
        // WhatsAppService::send($user->phone, "Seu código é: $otp");

        return response()->json([
            'message' => 'OTP enviado com sucesso',
            'otp_code' => $otp, // só pra ver no postman ###########apagar##########
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp_code' => 'required',
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('otp_code', $request->otp_code)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Código inválido ou expirado'], 401);
        }

        $user->otp_code = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    // Simula envio OTP (futuro: integrar Twilio, UltraMsg, etc.)
    public function loginSendOtp(Request $request)
    {
        $request->validate([
        'phone' => 'required',
    ]);

    // Verifica se o usuário existe
    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        // Usuário não encontrado → redireciona para registro
        return response()->json([
            'message' => 'Telefone não cadastrado. Redirecionando para o registro...',
        ], 404);
    }

    // Gera o código OTP e salva com expiração
    // $otp = rand(100000, 999999);
    $otp = 123;

    $user->update([
        'otp_code' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(5),
    ]);

    // Aqui enviaria o OTP por WhatsApp
    // WhatsAppService::send($user->phone, "Seu código é: $otp");

    return response()->json([
        'message' => 'OTP enviado com sucesso.',
        'otp_code' => $otp, // ########### APAGAR APÓS TESTE ###########
    ]);
    }
}
