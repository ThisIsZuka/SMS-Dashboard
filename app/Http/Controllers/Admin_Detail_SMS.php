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


class Admin_Detail_SMS extends BaseController
{
    public function list_sms(Request $request)
    {
        try {

            $data = $request->all();
            // dd($data);
            $return_data = new \stdClass();

            $DB_DATA = DB::table('dbo.LOG_SEND_SMS')
                ->select('SMS_ID', 'DATE', 'RUNNING_NO', 'QUOTATION_ID', 'APP_ID', 'TRANSECTION_TYPE', 'TRANSECTION_ID', 'SMS_RESPONSE_CODE', 'SMS_RESPONSE_MESSAGE', 'SMS_RESPONSE_JOB_ID', 'SEND_DATE', 'SEND_TIME', 'SEND_Phone', 'CONTRACT_ID', 'DUE_DATE')
                // ->get();
                ->where(function ($query) use ($data) {
                    if ($data['date_first'] != null) {
                        if ($data['date_last'] != null) {
                            $query->where('DATE', '>=', $data['date_first']);
                            $query->where('DATE', '<=', $data['date_last']);
                        } else {
                            $query->where('DATE', $data['date_first']);
                        }
                    }

                    if ($data['type'] != null) {
                        $query->where('TRANSECTION_TYPE', $data['type']);

                        if ($data['type_search'] != null) {
                            $query->where('TRANSECTION_ID', $data['type_search']);
                        }
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
                            $query->where($data['quick_select'], $data['quick_text']);
                        }
                    }
                })
                // ->distinct('TRANSECTION_ID')
                ->paginate($data['num_page']);

            $SMS_ = new \stdClass();
            $SMS_->list = $DB_DATA;

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


    public function SMS_Detail(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();

            $DB_DATA = DB::table('dbo.LOG_SEND_SMS')
                ->select('SMS_ID', 'DATE', 'RUNNING_NO', 'QUOTATION_ID', 'APP_ID', 'TRANSECTION_TYPE', 'TRANSECTION_ID', 'SMS_RESPONSE_CODE', 'SMS_RESPONSE_MESSAGE', 'SMS_RESPONSE_JOB_ID', 'SEND_DATE', 'SEND_TIME', 'SEND_Phone', 'CONTRACT_ID', 'DUE_DATE')
                ->where('TRANSECTION_TYPE', $data['transection_type'])
                ->where('TRANSECTION_ID', $data['transection_id'])
                ->get();

            // array_push($DB_DATA[0], 'tttttt');
            // dd(count($DB_DATA));
            for ($i = 0; $i < count($DB_DATA); $i++) {

                if ($DB_DATA[$i]->SMS_RESPONSE_JOB_ID == null) {
                    continue;
                }

                $DB_DATA_Detail = DB::table('dbo.LOG_SEND_SMS_DETAIL')
                    ->select('*')
                    ->where('SMS_JobID', $DB_DATA[$i]->SMS_RESPONSE_JOB_ID)
                    ->get();

                // dd($DB_DATA_Detail[0]);
                $DB_DATA[$i]->Detail_SMS = $DB_DATA_Detail;

            }


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
}
