<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;

use Illuminate\Support\Facades\Http;

class API_Service extends BaseController
{
    public function Send_SMS()
    {
        try {
            $dataNum = ['04512','012','1515'];
            $response = Http::get('http://ufund-portal.webhop.biz:9090/API-Corelease/api/master_prefix', [
                'name' => 'Taylor,456',
                'page' => '',
            ]);
            dd($response);
            // return $response;
        } catch (Exception $e) {
            return response()->json(array(
                'status' => 'Error',
                'message' => $e->getMessage()
            ));
        }
    }
}