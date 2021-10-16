<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;

class CheckApiToken extends BaseMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid'], 401);
            }
            if ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], 401);
            }

            return response()->json(['status' => 'Authorization Token not found'], 401);
        }
        return $next($request);

    }
}
