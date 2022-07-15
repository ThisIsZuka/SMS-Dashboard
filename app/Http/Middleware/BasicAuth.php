<?php

namespace App\Http\Middleware;

use Exception;
use Closure;
use Illuminate\Http\Request;

class BasicAuth
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

        try {

            $data = $request->all();
            if(!isset($data['USER']) || !isset($data['PW'])){
                return response()->view('auth_send_sms');
            }

            if($data['USER'] != 'admin' || $data['PW'] != 'admin'){
                // return response()->view('auth_send_sms');
                return response()->view('auth_send_sms');
            }
            

            return $next($request);
            // $AUTH_USER = 'admin';
            // $AUTH_PASS = 'admin';
            // header('Cache-Control: no-cache, must-revalidate, max-age=0');
            // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
            // $is_not_authenticated = (
            //     !$has_supplied_credentials ||
            //     $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            //     $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
            // );
            // if ($is_not_authenticated) {
            //     header('HTTP/1.1 401 Authorization Required');
            //     header('WWW-Authenticate: Basic realm="Access denied"');
            //     exit;
            // }
            // return $next($request);

        } catch (Exception $e) {
            return response()->view('auth_send_sms');
        }
    }
}
