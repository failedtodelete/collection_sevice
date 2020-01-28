<?php

namespace App\Http\Controllers;

use App\Facades\UserServiceFacade as UserService;
use App\Models\UserStatus;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    /**
     * Аутентификация пользователя.
     * Проверка статуса аккаунта и получение токена.
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Не верный логин или пароль'], 200);
        }

        $user = UserService::where('email', '=', $request->email)->first();
        if ($user->status_id !== UserStatus::ACTIVE) {
            return response()->json(['status' => false, 'message' => 'Пользователь не активен'], 200);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {

        if (!$token = $this->respondWithToken(auth()->refresh())) {
            return response()->json(['status' => false, 'message' => 'Ошибка сброса токена'], 400);
        }

        return $token;
    }
    /**
     * Get the token array structure.
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
