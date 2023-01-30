<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller {
    
    function __construct() {
        $this->middleware('admin')->only(['consulta']);
    }
    
    function login(Request $request) {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $key = 'secret_key';
        $payload = [
            'user' =>$request->input('email'),
            'expiration_date' => Carbon::now()->addHour(),
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        return response()->json([
            'jwt_token' => $jwt,
            'token_type' => 'Bearer'
        ], 200);
    }
    
    function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out']);
    }
    
    function consulta() {
        $latitude = '37.161259';
        $longitude = '-3.590709';
        $url = 'https://api.sunrise-sunset.org/json?lat='.$latitude.'&lng='.$longitude.'&date='.date('Y-m-d');
        $json = json_decode(file_get_contents($url));
        
        $sunrise = explode(":", $json->results->sunrise);
        $sunset = explode(":", $json->results->sunset);
        
        $from = $sunrise[0].':'.$sunrise[1];
        $to = $sunset[0].':'.$sunset[1];
        
        $auxInit = -pi()/2;
        $auxFinal = pi()/2;
        
        $actualHour = floatval(str_replace(":", ".", date('H:i')));
        $initHour = floatval(str_replace(":", ".", $from));
        $finalHour = floatval(str_replace(":", ".", $to));
        
        $cos = cos($auxInit + ( ($auxFinal - $auxInit) / ($finalHour - $initHour) ) * ($actualHour - $finalHour));
        $sin = sqrt(1 - pow($cos, 2));
        
        return response()->json([
            'cos' => $cos,
            'sin' => $sin,
            'sensor1' => rand(0, 1),
            'sensor2' => rand(0, 1),
            'sensor3' => rand(0, 1),
            'sensor4' => rand(0, 1)
        ], 200);
    }
    
}
