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

use App\Http\Controllers\Admin_Dashbord;

use Illuminate\Support\Facades\Event;
use App\Events\MyPusherEvent;
use App\Events\EventFinish_SMS;

class Admin_Detail_SMS extends BaseController
{
    public function list_sms(Request $request)
    {
        try {

            $data = $request->all();
            // dd($data);
            $return_data = new \stdClass();

            $DB_DATA = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->select('SMS_ID', 'DATE', 'RUNNING_NO', 'QUOTATION_ID', 'APP_ID', 'TRANSECTION_TYPE', 'TRANSECTION_ID', 'SMS_RESPONSE_CODE', 'SMS_RESPONSE_MESSAGE', 'SMS_RESPONSE_JOB_ID', 'SEND_DATE', 'SEND_TIME', 'SEND_Phone', 'CONTRACT_ID', 'DUE_DATE', 'SMS_TEXT_MESSAGE', 'SMS_CREDIT_USED', 'SMS_Status_Delivery')
                // ->get();
                ->where(function ($query) use ($data) {
                    if ($data['date_first'] != null) {
                        if ($data['date_last'] != null) {
                            $query->where(function ($subquery) use ($data) {
                                $subquery->where('DATE', '>=', $data['date_first']);
                                $subquery->where('DATE', '<=', $data['date_last']);
                            });
                        } else {
                            $query->where('DATE', $data['date_first']);
                        }
                    }

                    if ($data['type'] != null && $data['type'] != 'Other') {
                        $query->where('TRANSECTION_TYPE', $data['type']);

                        if ($data['type_search'] != null) {
                            $query->where('TRANSECTION_ID', $data['type_search']);
                        }
                    } else if ($data['type'] == 'Other') {
                        $query->where(function ($subquery) use ($data) {
                            $subquery->whereNotIn('TRANSECTION_TYPE', ['INVOICE', 'RECEIPT', 'TAX']);
                            $subquery->orwhereNull('TRANSECTION_TYPE');
                        });
                    }

                    if ($data['status'] != null) {
                        // $query->where('SMS_RESPONSE_CODE', $data['status'] == '000' ? '' :);
                        if ($data['status'] == '000') {
                            $query->where('SMS_RESPONSE_CODE', '000');
                        } else {
                            $query->where('SMS_RESPONSE_CODE', '!=', '000');
                        }
                    }

                    if ($data['due_date'] != null) {
                        $query->where('DUE_DATE', $data['due_date']);
                    }

                    if ($data['quick_select'] != null) {
                        if ($data['quick_text'] != null) {
                            $quick_text = $data['quick_text'];
                            if ($data['quick_select'] == 'SEND_PHONE') {
                                $phone_spec = str_replace(' ', '', $data['quick_text']);
                                $phone_subsrt = str_replace('-', '', $phone_spec);
                                $quick_text =  '66' . mb_substr($phone_subsrt, 1);
                                // dd($quick_text);
                            }
                            // $quick_text = 
                            $query->where($data['quick_select'], $quick_text);
                        }
                    }
                })
                // ->distinct('TRANSECTION_ID')
                ->orderBy('SMS_ID', 'ASC')
                ->paginate($data['num_page']);

            $SMS_ = new \stdClass();
            $SMS_->list = $DB_DATA;

            $return_data->data = $DB_DATA;
            $return_data->code = '999999';
            $return_data->message = 'Sucsess';

            $collect = collect($DB_DATA);
            $return_data->collect = $collect;

            return $return_data;
        } catch (Exception $e) {

            $return_data = new \stdClass();

            $return_data->code = '000000';
            $return_data->message =  $e->getMessage();

            return $return_data;
        }
    }


    public function SMS_Detail(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();

            $DB_DATA = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->select('SMS_ID', 'DATE', 'RUNNING_NO', 'QUOTATION_ID', 'APP_ID', 'TRANSECTION_TYPE', 'TRANSECTION_ID', 'SMS_RESPONSE_CODE', 'SMS_RESPONSE_MESSAGE', 'SMS_RESPONSE_JOB_ID', 'SEND_DATE', 'SEND_TIME', 'SEND_Phone', 'CONTRACT_ID', 'DUE_DATE', 'SMS_TEXT_MESSAGE', 'SMS_CREDIT_USED', 'SMS_Status_Delivery')
                // ->where('TRANSECTION_TYPE', $data['transection_type'])
                // ->where('TRANSECTION_ID', $data['transection_id'])
                ->where('SMS_ID', $data['sms_id'])
                ->get();

            // array_push($DB_DATA[0], 'tttttt');
            // dd(count($DB_DATA));

            $return_data->data = $DB_DATA;
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

    public function test_b(Request $request)
    {
        // for ($i = 0; $i < 15000; $i++) {
        //     $return_data = new \stdClass();
        //     $content = new Request();
        //     $report = new Admin_Dashbord();
        //     event(new MyPusherEvent($report->check_sender($content)));
        // }
        // $content_finish = new Request();
        // event(new EventFinish_SMS($content_finish));

        $return_data = new \stdClass();
        $return_data->code = '000000';

        $content = new Request();
        $content->data = '456';
        $content->data2 = '456';
        $content->data3 = $return_data;
        dd($content);
    }
}
