<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Res\Api;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function tokens($user)
    {
        $time = time();
        $expAccess = $time + 3600;
        $expRefresh = $time + 604800;

        try {
            $result = [
                "user"          => $user,
                "token_access"  => JWT::encode([
                    "user"  => $user,
                    "exp"   => $expAccess
                ], env("JWT_SECRET"), "HS256"),
                "token_refresh" => JWT::encode([
                    "user"  => $user,
                    "exp"   => $expRefresh
                ], env("JWT_REFRESH"), "HS256")
            ];

            return response()->json(Api::success($result));
        } catch (Exception $e) {
            return response()->json(Api::fails($e->getMessage()), 500);
        }
    }
}
