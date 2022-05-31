<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use ReallySimpleJWT\Token;

use Illuminate\Support\Facades\Hash;

class Login_Auth_Controller extends BaseController
{
    public function Get_Token(Request $request)
    {
        try {

            $data = $request->all();
            // dd($data);

            if (!isset($data['usp'])) throw new Exception('Required Parameter [usp]');
            if (!isset($data['paw'])) throw new Exception('Required Parameter [paw]');


            if ($data['usp'] != "k21dm102" || $data['paw'] != "Zg8>%z!!8DH~.AY% PG,b5(*KvP{mB%)_") {
                throw new Exception('Invalid Username or Password');
            }
            

            date_default_timezone_set("Asia/Bangkok");
            $now = Carbon::now();
            $exp = Carbon::now()->addMinute(60);
            $payload = [
                'iat' => $now,
                'uid' => 1,
                'exp' => $exp,
                'iss' => 'ufundportal.com',
                'user' => $data['usp']
            ];
            // dd($payload);
            // $secret = 'C0M$7uF0NdT0K@n';
            $secret = ENV('JWT_SECRET');

            $token = Token::customPayload($payload, $secret);

            $data_token = (array(
                // 'user' => $data['usp'],
                'expire' => 30 ,
                'k2-token' => $token,
            ));


            return response()->json(array(
                'Code' => '9999',
                'status' => 'Sucsess',
                'data' => $data_token,
            ));
        } catch (Exception $e) {
            return response()->json(array(
                'Code' => '00X2',
                'status' => 'Error',
                'message' => $e->getMessage(),
            ));
        }
    }
}
