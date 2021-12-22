<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        $cookie = $request->cookie('SMS_Username_Permission');
        if($cookie == 'admin') {
            return $next($request);
        }else{
            // return $next($request);
            abort(403, 'Unauthorized action.');
        }
    }
}
