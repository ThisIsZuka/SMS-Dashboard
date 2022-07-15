<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


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
        // print_r(Cookie::get());
        // $cookie = Cookie::get('SMS_Username_server');
        // if($request->hasCookie('SMS_Username_server')) {
        //     return $next($request);
        //     // abort(403, 'Unauthorized action.');
        // }else{
        //     return redirect()->route('login');
        // }
        // dd($request->cookie('SMS_Username_server'));
        if($request->hasCookie('SMS_Username_server')) {
            if($request->cookie('SMS_Username_server') != ''){
                return $next($request);  
            }
            return redirect()->route('login');
        }
        
        return redirect()->route('login');
        // $response = $next($request);
        // return $response->withCookie(cookie()->forever('SMS_Username_server', Cookie::get('SMS_Username_server')));
    }

}
