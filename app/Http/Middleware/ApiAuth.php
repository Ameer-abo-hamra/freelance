<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use App\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
class ApiAuth
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$guard): Response
    {
               if (!$request->header('Authorization')) {
                return $this->returnError("where is the token ?????",401);
            }

            // $token = $request->header('Authorization');
            // $token = explode(' ', $token)[1];
            $token = $request->bearerToken();
            echo $token ."\n";
            // echo JWTAuth::parseToken($token)->authenticate();
            // $user = Auth::guard($guard)->user();
            // JWTAuth::setToken(Auth::guard($guard)->user());
            // $user = JWTAuth::parseToken()->authenticate();

            if (!JWTAuth::setToken($token)->authenticate()) {
                return $this->returnError("Unauthorized",401);
            }

            return $next($request);
    }
}
