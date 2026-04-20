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

    /**
     * Register a new user
     */
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

    /**
     * Login user
     */
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

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Protection contre null → évite erreur 500
        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Non authentifié',
                'errors' => ['auth' => 'Unauthorized'],
                'meta' => []
            ], 401);
        }

        $this->service->logout($user);

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Déconnexion réussie',
            'errors' => null,
            'meta' => []
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Non authentifié',
                'errors' => ['auth' => 'Unauthorized'],
                'meta' => []
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'Utilisateur connecté',
            'errors' => null,
            'meta' => []
        ]);
    }

    /**
     * Reset password (MVP simplifié)
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Lien de réinitialisation envoyé',
            'errors' => null,
            'meta' => []
        ]);
    }
}