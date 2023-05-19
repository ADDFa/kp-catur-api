<?php

namespace App\Http\Middleware;

use App\Http\Res\Api;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(Api::fails("Unouhorized"), 401);

        try {
            $decode = JWT::decode($token, new Key(env("JWT_SECRET"), "HS256"));
            $request->user = $decode->user;

            return $next($request);
        } catch (Exception $e) {
            return response()->json(Api::fails($e->getMessage()), 500);
        }
    }
}
