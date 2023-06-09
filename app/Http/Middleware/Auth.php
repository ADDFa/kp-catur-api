<?php

namespace App\Http\Middleware;

use App\Http\Helper\Response;
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
        if (!$token) return response()->json(Response::fails("Unouhorized"), 401);

        try {
            $decode = JWT::decode($token, new Key(env("JWT_SECRET"), "HS256"));
            $request->user = $decode->user;

            return $next($request);
        } catch (Exception $e) {
            return response()->json(Response::fails($e->getMessage()), 500);
        }
    }
}
