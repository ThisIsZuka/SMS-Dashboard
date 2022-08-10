<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Exception;
use Carbon\Carbon;

use ReallySimpleJWT\Token;

use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Decode;

class JWT_Token
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
            $headers = $request->header();

            if(!isset($headers['k2-token'])) throw new Exception('Request header [k2-token]');

            date_default_timezone_set("Asia/Bangkok");

            $now = Carbon::now();

            $token = $headers['k2-token'][0];

            $secret = ENV('JWT_SECRET');

            // $result = Token::validate($token, $secret);
            $Header = Token::getHeader($token, $secret);

            // Return the payload claims
            $Payload = Token::getPayload($token, $secret);
            $crate_date = new Carbon($Payload['iat']);
            $crate_date->timezone("Asia/Bangkok");
            $exp = new Carbon($Payload['exp']);
            $exp->timezone("Asia/Bangkok");

            $jwt = new Jwt($token, $secret);
            // dd($jwt->getSecret());

            if($now > $exp){
                // throw new Exception('Token Expired');
                return response()->json(array(
                    'Code' => '00X1',
                    'status' => 'Error',
                    'message' => 'Token Expired',
                ));
            }

            return $next($request);

        } catch (Exception $e) {
            return response()->json(array(
                'Code' => '00X1',
                'status' => 'Error',
                'message' => $e->getMessage(),
                // 'message' => 'Invalid Token',
            ));
        }
    }
}
