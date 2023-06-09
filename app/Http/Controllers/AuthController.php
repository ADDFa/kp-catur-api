<?php

namespace App\Http\Controllers;

use App\Http\Helper\Response;
use App\Models\Credential;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function tokens($user)
    {
        try {
            $encode = function (int $exp, $keyname) use ($user) {
                $time = time();

                return JWT::encode([
                    "user"  => $user,
                    "exp"   => $time + $exp
                ], env($keyname), "HS256");
            };

            $result = [
                "user"          => $user,
                "token_access"  => $encode(3600, "JWT_SECRET"),
                "token_refresh" => $encode(604800, "JWT_REFRESH")
            ];

            return Response::success($result);
        } catch (Exception $e) {
            return Response::fails($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $credential = Credential::find($request->username);
        if (!$credential || !password_verify($request->password, $credential->password)) {
            return Response::fails("Username atau Password salah", 404);
        }

        $user = User::with("role")->find($credential->user_id);
        return $this->tokens($user);
    }

    public function refreshToken(Request $request)
    {
        try {
            $payload = JWT::decode($request->token_refresh, new Key(env("JWT_REFRESH"), "HS256"));
            return $this->tokens($payload->user);
        } catch (Exception $e) {
            return response()->json(Response::fails($e->getMessage()), 500);
        }
    }

    public function account(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "username"      => "required|string",
            "password"      => "required|min:8",
            "old_password"  => "required"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $credential = Credential::where("user_id", $id)->first();
        if (!$credential) return Response::fails("Unknown");

        if (!password_verify($request->old_password, $credential->password)) {
            return Response::fails("Password salah!");
        }

        $credential->username = $request->username;
        $credential->password = password_hash($request->password, PASSWORD_DEFAULT);
        $credential->save();

        return $this->tokens(User::find($credential->user_id));
    }
}
