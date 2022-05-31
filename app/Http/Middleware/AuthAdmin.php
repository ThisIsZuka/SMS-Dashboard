<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $cookie = Cookie::get('SMS_Username_server');
        if($cookie == 'admin') {
            return $next($request);
        }else{
            // return $next($request);
            abort(403, 'Unauthorized action.');
        }
    }
}
