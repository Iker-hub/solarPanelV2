<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        $key = 'secret_key';
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            $token = explode(' ', $header);
            if ($token) {
                try {
                    $decoded = JWT::decode($token[1], new Key($key, 'HS256'));
                    return $next($request);
                } catch(\Exception $e) {}
            }
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
