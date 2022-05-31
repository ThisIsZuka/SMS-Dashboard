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
use Illuminate\Support\Collection;
use Symfony\Component\VarDumper\Cloner\Data;

class API_Support_K2 extends BaseController
{
    public function Add_Signature(Request $request)
    {
        try{
            $input_data = $request->all();
            $dateNow = date("Y-m-d H:i:s");
            $APP_ID = isset($input_data['APP_ID']) ? $input_data['APP_ID'] : null ;
            // dd($input_data['pad_1']);
            
            $sql_pad_1 = null;
            $sql_pad_2 = null;
            $sql_pad_3 = null;
            $sql_pad_4 = null;
            $sql_pad_5 = null;

            if(isset($input_data['pad_1'])){
                $sql_pad_1 = DB::connection('sqlsrv_K2')->table('SmartBoxData.DigitalSignature')->insertGetId([
                    'Signature' => $input_data['pad_1'],
                    'Date' => $dateNow,
                    'FormURL' => null,
                    'UserFQN' => null,
                ]);
            }

            if(isset($input_data['pad_2'])){
                $sql_pad_2 = DB::connection('sqlsrv_K2')->table('SmartBoxData.DigitalSignature')->insertGetId([
                    'Signature' => $input_data['pad_2'],
                    'Date' => $dateNow,
                    'FormURL' => null,
                    'UserFQN' => null,
                ]);
            }

            if(isset($input_data['pad_3'])){
                $sql_pad_3 = DB::connection('sqlsrv_K2')->table('SmartBoxData.DigitalSignature')->insertGetId([
                    'Signature' => $input_data['pad_3'],
                    'Date' => $dateNow,
                    'FormURL' => null,
                    'UserFQN' => null,
                ]);
            }

            if(isset($input_data['pad_4'])){
                $sql_pad_4 = DB::connection('sqlsrv_K2')->table('SmartBoxData.DigitalSignature')->insertGetId([
                    'Signature' => $input_data['pad_4'],
                    'Date' => $dateNow,
                    'FormURL' => null,
                    'UserFQN' => null,
                ]);
            }

            if(isset($input_data['pad_5'])){
                $sql_pad_5 = DB::connection('sqlsrv_K2')->table('SmartBoxData.DigitalSignature')->insertGetId([
                    'Signature' => $input_data['pad_5'],
                    'Date' => $dateNow,
                    'FormURL' => null,
                    'UserFQN' => null,
                ]);
            }
            
            $return_data = new \stdClass();

            $return_data->Code = '000000';
            $return_data->Status =  'test555';

            return $return_data;
        } catch(Exception $e){
            $return_data = new \stdClass();

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }
}

?>