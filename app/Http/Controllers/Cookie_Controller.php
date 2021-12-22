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


class Cookie_Controller extends BaseController
{

    public function Get_cookieByName(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();
            // dd(count($data['cookie']));
            $obj_cookie = new \stdClass();

            $list_value=array();

            for($i = 0 ; $i < count($data['cookie']); $i++){
                
                $obj_cookie = new \stdClass();
                $obj_cookie->name = $data['cookie'][$i];
                $obj_cookie->value = $request->cookie($data['cookie'][$i]);

                array_push($list_value, $obj_cookie);
            }
            
           
            // dd($value);

            $return_data->data = $list_value;
            $return_data->code = '999999';
            $return_data->message = 'Sucsess';

            return $return_data;

        } catch (Exception $e) {

            $return_data = new \stdClass();

            $return_data->code = '000000';
            $return_data->message =  $e->getMessage();

            return $return_data;
        }
    }
}
