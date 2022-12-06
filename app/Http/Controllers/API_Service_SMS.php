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

use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\ConnectionException;

use Illuminate\Support\Facades\Event;
use App\Events\MyPusherEvent;
use App\Events\EventFinish_SMS;

use App\Jobs\SendSMS;
use App\Jobs\Job_QueueSMS;
use App\Jobs\Job_QueueCheckSMSDeliver;
use stdClass;

class API_Service_SMS extends BaseController
{

    public static $MAILBIT_USER;
    public static $MAILBIT_PASS;
    public static $MAILBIT_sid;

    public static $MAILBIT_APIKey;
    public static $MAILBIT_ClientID;
    public static $MAILBIT_SenderId;

    public function __construct()
    {
        self::$MAILBIT_USER = config('global_variable.MAILBIT_USER');
        self::$MAILBIT_PASS = config('global_variable.MAILBIT_PASS');
        self::$MAILBIT_sid = config('global_variable.MAILBIT_sid');

        self::$MAILBIT_APIKey = config('global_variable.MAILBIT_APIKey');
        self::$MAILBIT_ClientID = config('global_variable.MAILBIT_ClientID');
        self::$MAILBIT_SenderId = config('global_variable.MAILBIT_SenderId');
    }

    public function PostRequest_SMS($url, $_data)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->post($url, $_data);
        $resData =  $response->body();

        return array($resData, $_data['message']);
    }

    public function submit_send_SMS_Invoice(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();
            // dd('dd');
            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');
            $inv_date = $data['INV_DATE'];

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                ->where('date', $dateNow)
                ->get();

            $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '" . $inv_date . "' "));

            // for ($i = 0; $i < count($list_sendSMS); $i++) {
            for ($i = 0; $i < 1; $i++) {

                try {
                    $phone = '66' . mb_substr($list_sendSMS[$i]->PHONE, 1);

                    // Convert Date to text
                    $split_str = explode("-", $list_sendSMS[$i]->DUE_DATE);

                    $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
                    $strMonthThai = $strMonthCut[(int)$split_str[1]];

                    $year = substr(($split_str[0] + 543), -2);

                    $textDate = $split_str[2] . " " . $strMonthThai . " " . $year;

                    $data_arry = array(
                        'apiKey' => self::$MAILBIT_APIKey,
                        'clientId' => self::$MAILBIT_ClientID,
                        'mobileNumbers' => $phone,
                        'SenderId' => self::$MAILBIT_SenderId,
                        'message' => "UFUND ส่งบิล รอบกำหนดชำระ " . $textDate . " กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก " . $list_sendSMS[$i]->SHT_INV_URL . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
                        'is_Unicode' => true,
                        'is_Flash' => false,
                    );

                    list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

                    $obj2 = json_decode($content);
                    // dd($obj2);

                    $MSG_ID = null;
                    $SumCredit = null;
                    if (!is_null($obj2->Data)) {
                        $MSG_ID = $obj2->Data[0]->MessageId;
                        $SumCredit = count($obj2->Data);
                    }


                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
                        // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->DUE_DATE,
                        'SMS_RESPONSE_MSG_ID' => $MSG_ID,
                        'SMS_TEXT_MESSAGE' => $data_arry['message'],
                        'SMS_CREDIT_USED' => $SumCredit,
                    ]);
                } catch (Exception $e) {

                    $new_error_id = date("Ymdhis");

                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => '0x00',
                        'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                        'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->DUE_DATE,
                        'SMS_TEXT_MESSAGE' => $e->getMessage(),
                    ]);

                    $return_data = new \stdClass();

                    $return_data->Code = '0X0MB0000';
                    $return_data->Status =  $e->getMessage();

                    return $return_data;
                }

                // $content = new Request();
                // $report = new Admin_Dashbord();
                // event(new MyPusherEvent($report->check_sender($content)));
                // event(new MyPusherEvent($content));
            }

            // $content_finish = new Request();
            // event(new EventFinish_SMS($content_finish));

            $return_data->Code = '999999';
            $return_data->Status = 'Success';
            return $return_data;
            // dd($obj2);
            // echo $content;

        } catch (Exception $e) {
            $return_data = new \stdClass();

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }

    public function SMS_Check_Credit()
    {
        $response = Http::withHeaders([
            'contentType' => 'application/json'
        ])->get('https://api.send-sms.in.th/api/v2/Balance', [
            'ApiKey' => self::$MAILBIT_APIKey,
            'ClientId' => self::$MAILBIT_ClientID,
        ]);

        $res_data = json_decode($response->body());
        return $res_data;
    }

    public function submit_send_SMS_Invoice_optimize(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();
            $inv_date = $data['INV_DATE'];

            $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '" . $inv_date . "' "));

            for ($i = 0; $i < count($list_sendSMS); $i++) {

                Job_QueueSMS::dispatch($list_sendSMS[$i])->onQueue('site_main');
            }

            $return_data->Code = '999999';
            $return_data->Status = 'SMS Processing';
            $return_data->SMS = count($list_sendSMS);
            return $return_data;
        } catch (Exception $e) {
            $return_data = new \stdClass();

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }




    public function submit_send_SMS_WelcomeCall(Request $request)
    {
        try {

            $data = $request->all();

            if (!isset($data['DATE_SEND'])) {
                return response()->json(array(
                    'Code' => '000000',
                    'Status' => 'Request Parameter[DATE_SEND]',
                ));
            }

            $return_data = new \stdClass();
            // dd($data['number']);
            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');
            $Send_date = $data['DATE_SEND'];

            $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_SEND_SMSWelcomeCall  @date_send = '" . $Send_date . "' "));
            // dd($list_sendSMS);


            for ($i = 0; $i < count($list_sendSMS); $i++) {

                $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                    ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                    ->where('date', $dateNow)
                    ->get();

                $phone = '66' . mb_substr($list_sendSMS[$i]->PHONE, 1);

                try {

                    if ($list_sendSMS[$i]->CONTRACT_ID == null || $list_sendSMS[$i]->CONTRACT_ID == '') {
                        throw new Exception('Invalid CONTRACT_ID');
                    }

                    $data_arry = array(
                        'apiKey' => self::$MAILBIT_APIKey,
                        'clientId' => self::$MAILBIT_ClientID,
                        'mobileNumbers' => $phone,
                        'SenderId' => self::$MAILBIT_SenderId,
                        'message' => $list_sendSMS[$i]->sms,
                        'is_Unicode' => true,
                        'is_Flash' => false,
                    );

                    list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

                    $obj2 = json_decode($content);
                    // dd($obj2);
                    $datestamp = date('Y-m-d');
                    $timestamp = date('H:i:s');

                    $MSG_ID = null;
                    $SumCredit = null;
                    if (!is_null($obj2->Data)) {
                        $MSG_ID = $obj2->Data[0]->MessageId;
                        $SumCredit = count($obj2->Data);
                    }


                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => $list_sendSMS[$i]->type_sms,
                        'TRANSECTION_ID' => null,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
                        // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'SMS_RESPONSE_MSG_ID' => $MSG_ID,
                        'SMS_TEXT_MESSAGE' => $data_arry['message'],
                        'SMS_CREDIT_USED' => $SumCredit,
                    ]);


                    if ($obj2->ErrorCode == 0) {
                        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_wellcomeCall')
                            ->where('id',  $list_sendSMS[$i]->id)
                            ->update([
                                'STATUS_SEND' => 'true',
                            ]);
                    } else {
                        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_wellcomeCall')
                            ->where('id',  $list_sendSMS[$i]->id)
                            ->update([
                                'STATUS_SEND' => 'false',
                            ]);
                    }
                } catch (Exception $e) {
                    // dd($e->getMessage());
                    date_default_timezone_set('Asia/bangkok');
                    // $dateNow = date('Y-m-d');
                    $datestamp = date('Y-m-d');
                    $timestamp = date('H:i:s');
                    $new_error_id = date("Ymdhis");
                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => $list_sendSMS[$i]->type_sms,
                        'TRANSECTION_ID' => null,
                        'SMS_RESPONSE_CODE' => '0x00',
                        'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                        'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'SMS_TEXT_MESSAGE' => $e->getMessage(),
                    ]);

                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_wellcomeCall')
                        ->where('id',  $list_sendSMS[$i]->id)
                        ->update([
                            'STATUS_SEND' => 'false',
                        ]);
                }
            }

            $return_data->Code = '999999';
            $return_data->Status = 'Success';
            return $return_data;
        } catch (Exception $e) {
            $return_data = new \stdClass();

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }


    public function submit_send_SMS_Garantor(Request $request)
    {
        $data = $request->all();

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');


        if (!isset($data['ID'])) {
            return response()->json(array(
                'Code' => '000000',
                'Status' => 'Request Parameter[Type , Phone]',
            ));
        }

        // $phone = '66' . mb_substr('0923629449', 1);
        $phone = '66' . mb_substr($data['Phone'], 1);
        $return_data = new \stdClass();
        // dd($data['Type']);

        $object_type = new \stdClass();
        $object_type->NOTPASS = 'UFund ขอขอบคุณที่ท่านสนใจให้การสมัคร ผู้ค้ำประกันของท่าน ไม่ผ่านเกณฑ์พิจารณาเบื้องต้น กรุณาตรวจสอบอีเมลของท่าน เพื่อเปลี่ยนผู้ค้ำประกัน';
        $object_type->NOTACCEPT = 'UFund ขอขอบคุณที่ท่านสนใจให้การสมัคร ผู้ค้ำประกันของท่าน ไม่ยินยอมในการค้ำประกัน กรุณาตรวจสอบอีเมลของท่าน เพื่อเปลี่ยนผู้ค้ำประกัน';
        $object_type->CHANGEGUA = 'UFund ขอขอบคุณที่ท่านสนใจให้การสมัคร และเข้ามายืนยันตัวตน ผู้ค้ำประกันของท่านไม่ผ่านเกณฑ์การพิจารณาของบริษัท กรุณาตรวจสอบอีเมลของท่านเพื่อเปลี่ยนผู้ค้ำประกัน';

        $type = $data['Type'];
        // dd($object_type->$type);

        $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            ->where('date', $dateNow)
            ->get();

        try {


            $data_arry = array(
                'apiKey' => self::$MAILBIT_APIKey,
                'clientId' => self::$MAILBIT_ClientID,
                'mobileNumbers' => $phone,
                'SenderId' => self::$MAILBIT_SenderId,
                'message' => $object_type->$type,
                'is_Unicode' => true,
                'is_Flash' => false,
            );

            // dd($data_arry['msg']);
            list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

            $obj2 = json_decode($content);

            $MSG_ID = null;
            $SumCredit = null;
            if (!is_null($obj2->Data)) {
                $MSG_ID = $obj2->Data[0]->MessageId;
                $SumCredit = count($obj2->Data);
            }


            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'TRANSECTION_TYPE' => 'Garantor',
                'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
                'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
                // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'SMS_RESPONSE_MSG_ID' => $MSG_ID,
                'SMS_TEXT_MESSAGE' => $data_arry['message'],
                'SMS_CREDIT_USED' => $SumCredit,
            ]);
            // dd($obj2);
        } catch (Exception $e) {

            $new_error_id = date("Ymdhis");

            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'TRANSECTION_TYPE' => 'Garantor',
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'SMS_TEXT_MESSAGE' => $e->getMessage(),
            ]);

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }


    public function SMS_send_ByType(Request $request)
    {
        $data = $request->all();
        $return_data = new \stdClass();

        // return $data;

        if (!isset($data['ID']) || !isset($data['Status_sms'])) {
            return response()->json(array(
                'Code' => '000000',
                'Status' => 'Request Parameter[ID , Status_sms]',
            ));
        }

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');
        $ID = $data['ID'];
        $Status_sms = $data['Status_sms'];

        $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            ->where('date', $dateNow)
            ->get();


        $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_SEND_SMS  @ID = '" . $ID . "' , @Status_sms = " . $Status_sms));

        if (count($list_sendSMS) == 0) {
            return response()->json(array(
                'Code' => '0050',
                'status' => 'Error',
                'message' => 'Empty Data',
            ));
        }
        // dd($list_sendSMS);
        $phone_remove_special = str_replace('-', '', $list_sendSMS[0]->phone);
        $phone = '66' . mb_substr($phone_remove_special, 1);
        // $phone = '66957498908';

        try {

            $message_sms_tel = str_replace("[tel]", $list_sendSMS[0]->phone_BRANCH, $list_sendSMS[0]->MESSAGE);
            $message_sms_contract = str_replace("[CONTRACT_NUMBER]", $list_sendSMS[0]->CONTRACT_NUMBER, $message_sms_tel);
            $message_sms_app = str_replace("[Approve]", (isset($list_sendSMS[0]->APPROVE_CODE) ? $list_sendSMS[0]->APPROVE_CODE : null), $message_sms_contract);

            // $data_arry = array(
            //     'user' => self::$MAILBIT_USER,
            //     'password' => self::$MAILBIT_PASS,
            //     'msisdn' => $phone,
            //     'sid' => self::$MAILBIT_sid,
            //     'msg' => $message_sms_app,
            //     'fl' => "0",
            //     'dc' => "8",
            // );
            $data_arry = array(
                'apiKey' => self::$MAILBIT_APIKey,
                'clientId' => self::$MAILBIT_ClientID,
                'mobileNumbers' => $phone,
                'SenderId' => self::$MAILBIT_SenderId,
                'message' => $message_sms_app,
                'is_Unicode' => true,
                'is_Flash' => false,
            );

            list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

            $obj2 = json_decode($content);

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $MSG_ID = null;
            $SumCredit = null;
            if (!is_null($obj2->Data)) {
                $MSG_ID = $obj2->Data[0]->MessageId;
                $SumCredit = count($obj2->Data);
            }

            $DUE_DATE = isset($list_sendSMS[0]->DUE_DATE) ? $list_sendSMS[0]->DUE_DATE : null;
            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $list_sendSMS[0]->QUOTATION_ID,
                'APP_ID' => $list_sendSMS[0]->APP_ID,
                'TRANSECTION_TYPE' => null,
                'TRANSECTION_ID' => null,
                'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
                'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
                // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'CONTRACT_ID' => $list_sendSMS[0]->Contract_id,
                'DUE_DATE' => $DUE_DATE,
                'SMS_RESPONSE_MSG_ID' => $MSG_ID,
                'SMS_TEXT_MESSAGE' => $data_arry['message'],
                'SMS_CREDIT_USED' => $SumCredit,
            ]);


            return response()->json(array(
                'Code' => '9999',
                'status' => 'Success',
            ));

            // dd($list_sendSMS);
        } catch (Exception $e) {

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');
            $new_error_id = date("Ymdhis");
            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $list_sendSMS[0]->QUOTATION_ID,
                'APP_ID' => $list_sendSMS[0]->APP_ID,
                'TRANSECTION_TYPE' => null,
                'TRANSECTION_ID' => null,
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'CONTRACT_ID' => $list_sendSMS[0]->Contract_id,
                'SMS_TEXT_MESSAGE' => $e->getMessage(),
            ]);

            $return_data->Code = '000000';
            $return_data->Message =  $e->getMessage();

            return $return_data;
        }
    }


    public function queueTest()
    {
        try {

            // $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '01-01-2022' "));

            // for ($x = 1; $x < 10; $x++) {
            //   // var_dump($x);
            // //   if($x % 2 == 0){
            // //     CalculateDataJob::dispatch($list_sendSMS[$x]);
            // //   }else{
            // //     CalculateDataJob::dispatch($list_sendSMS[$x]);
            // //   }
            // Job_QueueSMS::dispatch($list_sendSMS[$x]);
            // }


            $LOG_SEND_SMS = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->select('SMS_RESPONSE_MSG_ID', 'SMS_Status_Delivery')
                ->where('SMS_Status_Delivery', '')
                ->orWhereNull('SMS_Status_Delivery')
                ->get();

            if (count($LOG_SEND_SMS) <= 0) return count($LOG_SEND_SMS);

            echo '456';
            // $response = Http::get('https://api-uat.partners.scb/partners/v1/payment/billpayment/transactions/' . $data['transectionId'] . '?sendingBank=014');

            return $LOG_SEND_SMS;
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function check_sending(Request $request)
    {

        try {

            $data = $request->all();

            $user = self::$MAILBIT_USER;
            $password = self::$MAILBIT_PASS;

            $response = Http::get('http://sms.mailbit.co.th/vendorsms/checkdelivery.aspx?user=' . $user . '&password=' . $password . '&messageid=' . $data['msgId']);

            return $response;
        } catch (Exception $e) {

            return response()->json(array(
                'Code' => '0500',
                'status' => 'Failed',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function TestSending()
    {
        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');

        $phone = '66' . mb_substr('0804817163', 1);

        $return_data = new \stdClass();

        // dd($object_type->$type);

        $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            ->where('date', $dateNow)
            ->get();

        try {

            $data_arry = array(
                'apiKey' => self::$MAILBIT_APIKey,
                'clientId' => self::$MAILBIT_ClientID,
                'mobileNumbers' => $phone,
                'SenderId' => self::$MAILBIT_SenderId,
                'message' => 'Test New sending ทดสอบ',
                'is_Unicode' => true,
                'is_Flash' => false,
            );

            // dd($data_arry['msg']);
            list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

            $obj2 = json_decode($content);

            $MSG_ID = null;
            $SumCredit = null;
            if (!is_null($obj2->Data)) {
                $MSG_ID = $obj2->Data[0]->MessageId;
                $SumCredit = count($obj2->Data);
            }


            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'TRANSECTION_TYPE' => 'Garantor',
                'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
                'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
                // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'SMS_RESPONSE_MSG_ID' => $MSG_ID,
                'SMS_TEXT_MESSAGE' => $data_arry['message'],
                'SMS_CREDIT_USED' => $SumCredit,
            ]);
            // dd($obj2);

            return response()->json(array(
                'Code' => '9999',
                'status' => 'Success',
            ));
            
        } catch (Exception $e) {

            $new_error_id = date("Ymdhis");

            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'TRANSECTION_TYPE' => 'Garantor',
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'SMS_TEXT_MESSAGE' => $e->getMessage(),
            ]);

            $return_data->Code = '000000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }

    public function test_new()
    {
        $LOG_SEND_SMS = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->select('SMS_ID', 'SMS_RESPONSE_MSG_ID', 'SMS_Status_Delivery')
            ->where('SMS_RESPONSE_CODE', '000')
            ->where(function ($query) {
                $query->where('SMS_Status_Delivery', '');
                $query->orWhereNull('SMS_Status_Delivery');
            })
            ->get();

        if (count($LOG_SEND_SMS) == 0) return 0;

        for ($i = 0; $i < count($LOG_SEND_SMS); $i++) {

            Job_QueueCheckSMSDeliver::dispatch($LOG_SEND_SMS[$i])->onQueue('site_main');
        }

        $return_data = new stdClass;
        $return_data->Code = '999999';
        $return_data->Status = 'SMS Processing';
        return $return_data;
    }
}
