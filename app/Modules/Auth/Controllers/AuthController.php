<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->service->register($request->validated());

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Utilisateur créé',
            'errors' => null,
            'meta' => []
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->service->login($request->validated());

        if (!$result) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Identifiants invalides',
                'errors' => ['auth' => 'Unauthorized'],
                'meta' => []
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Connexion réussie',
            'errors' => null,
            'meta' => []
        ]);
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Déconnexion réussie',
            'errors' => null,
            'meta' => []
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Utilisateur connecté',
            'errors' => null,
            'meta' => []
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        // Simplifié (MVP)
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Lien de réinitialisation envoyé',
            'errors' => null,
            'meta' => []
        ]);
    }
}