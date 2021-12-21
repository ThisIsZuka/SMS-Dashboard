<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthLogin
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
        $cookie = $request->cookie('SMS_Username_server');
        if($cookie == null) {
            return redirect()->route('login');
            // abort(403, 'Unauthorized action.');
        }else{
            return $next($request);
        }
    }
}
