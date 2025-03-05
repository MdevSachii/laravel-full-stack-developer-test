<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        try {
            $secret = env('JWT_SECRET', 'your-secret-key');
            $payload = app(TokenService::class)->decodeToken($token, $secret);
           

            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return response()->json(['error' => 'Token has expired'], 401);
            }

            $request['jwt_payload'] = $payload;

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
        return $next($request);
    }
}
