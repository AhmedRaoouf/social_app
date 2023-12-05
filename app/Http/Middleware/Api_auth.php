<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Api_auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token =$request->header('auth_token');
        if($token !== null)
        {
            $user = User::where('token',$token)->first();
            if($user !==null)
            {
                return $next($request);
            }else{
                return response()->json([
                    'message'=>"token not valid"
                ]);
            }
        }else{
            return response()->json([
                'message'=>"token not sent"
            ]);
        }
    }
}
