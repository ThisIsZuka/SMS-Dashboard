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
use Illuminate\Http\Client\ConnectionException;
use Config;

use App\Jobs\TestJob;

class Queue_Job extends BaseController
{

    public static $API_Key;

    public function __construct()
    {
        // $this->API_Key =config('global_variable.SCB_API_Key');
        self::$API_Key = config('global_variable.SCB_API_Key');
    }

    public function GetQueue()
    {
        $connection = null;
        $default = 'default';

        // For the delayed jobs
        var_dump(
            \Queue::getRedis()
                ->connection($connection)
                ->zrange('queues:' . $default . ':delayed', 0, -1)
        );

        // For the reserved jobs
        var_dump(
            \Queue::getRedis()
                ->connection($connection)
                ->zrange('queues:' . $default . ':reserved', 0, -1)
        );
    }

    public function TestScheduling()
    {

        // return $response;

        // $LOG_SEND_SMS = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //     ->select('SMS_ID', 'SMS_RESPONSE_MSG_ID', 'SMS_Status_Delivery')
        //     ->where('SMS_RESPONSE_CODE', '000')
        //     ->where(function ($query) {
        //         $query->where('SMS_Status_Delivery', '');
        //         $query->orWhereNull('SMS_Status_Delivery');
        //     })
        //     ->toSql();


        // if(count($LOG_SEND_SMS) == 0) return 0;

        // dd($LOG_SEND_SMS);

        // return '456';
        try {

            // return self::$API_Key;
            // $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '01-01-2022' "));
            // dd($list_sendSMS[10]->QUOTATION_ID);
            // count($list_sendSMS);
            for ($x = 0; $x < 1; $x++) {
                // var_dump($list_sendSMS[$x]);
                TestJob::dispatch('thailand')->onQueue('site_sms');
            }

            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
