<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Date;

class PageLogin_Controller extends BaseController
{

    public function Login_user(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();

            $minutes = time() + 60 * 60 * 6;
            if($data['username'] == 'admin' && $data['password'] == 'k2admin'){
                Cookie::queue('SMS_Username_server', $data['username'], $minutes);
                // Cookie::queue('SMS_Username_Permission', 'admin', $minutes);
                $return_data->code = '999999';
                $return_data->message = 'Sucsess';
            }else{
                // Cookie::queue('SMS_Username_Permission', 'user', $minutes);
                $return_data->code = '890000';
                $return_data->message = 'Sucsess';
            }
            
            return $return_data;

        } catch (Exception $e) {

            $return_data = new \stdClass();

            $return_data->code = '000000';
            $return_data->message =  $e->getMessage();

            return $return_data;
        }
    }
}
