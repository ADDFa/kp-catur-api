<?php

namespace App\Http\Controllers;

use App\Http\Res\Api;
use App\Models\Credential;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // get user credential
        $credential = Credential::find($request->username);
        if (!$credential) return response()->json(Api::fails("Username atau Password salah"), 404);

        // cek password
        if (!password_verify($request->password, $credential->password)) {
            return response()->json(Api::fails("Username atau Password salah"), 404);
        }

        // generate token
        return $this->tokens(User::with("position")->find($credential->user_id));
    }

    public function refreshToken(Request $request)
    {
        try {
            $payload = JWT::decode($request->token_refresh, new Key(env("JWT_REFRESH"), "HS256"));
            return $this->tokens($payload->user);
        } catch (Exception $e) {
            return response()->json(Api::fails($e->getMessage()), 500);
        }
    }
}
