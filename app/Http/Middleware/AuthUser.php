<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthUser
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
        if(!$request->bearerToken()){
            return response()->json(array("message"=>"unauthenticated", "errors"=>["authentication token must be sent"]), 401);//401
        }
        if (auth()->user()) {
            $request['player'] = auth()->user()->toArray();
            return $next($request);
        }
        return response()->json(array("message"=>"unauthenticated","errors"=>["invalid authentication token"]), 401);//401
    }
}
