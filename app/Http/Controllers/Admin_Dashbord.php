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


class Admin_Dashbord extends BaseController
{

    public function check_sender(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();

            $DB_DATA = DB::table('dbo.LOG_SEND_SMS_DETAIL')
                ->count();

            $DB_DATA_Sum = DB::table('dbo.LOG_SEND_SMS')
                ->count();

            $SMS_ = new \stdClass();
            $SMS_->sms_credit = $DB_DATA;
            $SMS_->sms_sum = $DB_DATA_Sum;

            $return_data->data = $SMS_;
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


    public function check_sender_type(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();
            $DB_INV = DB::table('dbo.LOG_SEND_SMS')
                ->where('TRANSECTION_TYPE', 'INVOICE')
                ->where(function($query) use ($data)  {
                    if($data['month'] != 0){
                        $query->whereMonth('DATE',$data['month']);
                    }
                 })
                // ->whereMonth('DATE',$data['month'])
                ->whereYear('DATE', $data['year'])
                ->count();
            $DB_REC = DB::table('dbo.LOG_SEND_SMS')
                ->where('TRANSECTION_TYPE', 'RECEIPT')
                ->where(function($query) use ($data)  {
                    if($data['month'] != 0){
                        $query->whereMonth('DATE',$data['month']);
                    }
                 })
                // ->whereMonth('DATE', $data['month'])
                ->whereYear('DATE', $data['year'])
                ->count();

            $DB_TAX = DB::table('dbo.LOG_SEND_SMS')
                ->where('TRANSECTION_TYPE', 'TAX')
                ->where(function($query) use ($data)  {
                    if($data['month'] != 0){
                        $query->whereMonth('DATE',$data['month']);
                    }
                 })
                // ->whereMonth('DATE', $data['month'])
                ->whereYear('DATE', $data['year'])
                ->count();

            $sms_INV = new \stdClass();
            $sms_INV->type = 'INVOICE';
            $sms_INV->txt_name = 'SMS INVOICE';
            $sms_INV->sum = $DB_INV;

            $sms_REC = new \stdClass();
            $sms_REC->type = 'RECEIPT';
            $sms_REC->txt_name = 'SMS RECEIPT';
            $sms_REC->sum = $DB_REC;

            $sms_TAX = new \stdClass();
            $sms_TAX->type = 'TAX';
            $sms_TAX->txt_name = 'SMS TAX';
            $sms_TAX->sum = $DB_TAX;

            $arry_list = array($sms_INV, $sms_REC, $sms_TAX);

            $return_data->data = $arry_list;
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