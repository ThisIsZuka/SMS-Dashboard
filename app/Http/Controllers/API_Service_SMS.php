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

class API_Service_SMS extends BaseController
{

    public function SMS_Check_Credit()
    {
        $data = array(
            'user' => "ufund_official",
            'password' => "ufund@2022",
            'sid' => "UFUND TH",
        );
        list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/CheckBalance.aspx", 'www.comseven.com', $data);
        return $content;
    }

    public function PostRequest_SMS($url, $referer, $_data)
    {
        // dd($_data["msisdn"]);
        // // convert variables array to string:
        $data = array();
        foreach ($_data as $key => $value) {
            // echo $key;
            $data[] = "$key=$value";
        }
        $data = implode('&', $data);
        // dd($data);
        // format --> test1=a&test2=b etc.
        // parse the given URL
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            die('Only HTTP request are supported !');
        }
        // dd($url);
        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];
        // open a socket connection on port 80
        $fp = fsockopen($host, 80);
        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Referer: $referer\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($data) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);
        $result = '';
        while (!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
        // close the socket connection:
        fclose($fp);
        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
        // return as array:
        return array($header, $content);
    }

    public function submit_send_SMS_Invoice(Request $request)
    {
        try {

            $data = $request->all();
            $return_data = new \stdClass();
            dd('dd');
            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');
            $inv_date = $data['INV_DATE'];

            $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '" . $inv_date . "' "));

            for ($i = 0; $i < count($list_sendSMS); $i++) {

                try {
                    $phone = '66' . mb_substr($list_sendSMS[$i]->PHONE, 1);

                    // Convert Date to text
                    $split_str = explode("-", $list_sendSMS[$i]->DUE_DATE);

                    $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
                    $strMonthThai = $strMonthCut[(int)$split_str[1]];

                    $year = substr(($split_str[0] + 543), -2);

                    $textDate = $split_str[2] . " " . $strMonthThai . " " . $year;
                    // dd($textDate);
                    // printf();
                    $data_arry = array(
                        'user' => "ufund_official",
                        'password' => "ufund@2022",
                        'msisdn' => $phone,
                        'sid' => "UFUND TH",
                        // 'msg' => "UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=" . $list_sendSMS[$i]->INVOICE_ID,
                        // 'msg' => "UFUND ส่งบิล รอบกำหนดชำระ ".$textDate." กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=" . $list_sendSMS[$i]->INVOICE_ID . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
                        'msg' => "UFUND ส่งบิล รอบกำหนดชำระ " . $textDate . " กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก " . $list_sendSMS[$i]->SHT_INV_URL . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
                        'fl' => "0",
                        'dc' => "8",
                    );

                    // echo '<pre>';
                    // print_r($data_arry);
                    // echo '</pre>';
                    list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data_arry);

                    $obj2 = json_decode($content);
                    // dd($obj2);
                    $datestamp = date('Y-m-d');
                    $timestamp = date('H:i:s');

                    $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                        ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                        ->where('date', $dateNow)
                        ->get();


                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                        'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->DUE_DATE,
                    ]);


                    if ($obj2->MessageData) {
                        $txt_message = '';
                        $msg = $obj2->MessageData[0]->MessageParts;
                        for ($x = 0; $x < count($msg); $x++) {
                            $txt_message .=  $msg[$x]->Text;
                        }
                        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                            ->where('SMS_RESPONSE_JOB_ID',  $obj2->JobId)
                            ->update([
                                'SMS_RESPONSE_MSG_ID' => $msg[0]->MsgId,
                                'SMS_TEXT_MESSAGE' => $txt_message,
                                'SMS_CREDIT_USED' => count($msg),
                            ]);
                    }
                } catch (Exception $e) {
                    date_default_timezone_set('Asia/bangkok');
                    $datestamp = date('Y-m-d');
                    $timestamp = date('H:i:s');
                    $new_error_id = date("Ymdhis");
                    $return_data = new \stdClass();

                    $return_data->Code = '0X0MB0000';
                    $return_data->Status =  $e->getMessage();

                    return $return_data;
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
                }
            }


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
                        'user' => "ufund_official",
                        'password' => "ufund@2022",
                        'msisdn' => $phone,
                        'sid' => "UFUND TH",
                        // 'msg' => "UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=" . $list_sendSMS[$i]->INVOICE_ID,
                        'msg' => $list_sendSMS[$i]->sms,
                        'fl' => "0",
                        'dc' => "8",
                    );

                    list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data_arry);
                    $obj2 = json_decode($content);
                    // dd($obj2);
                    $datestamp = date('Y-m-d');
                    $timestamp = date('H:i:s');


                    DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => $list_sendSMS[$i]->type_sms,
                        'TRANSECTION_ID' => null,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                        'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $datestamp,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                    ]);


                    if ($obj2->MessageData) {
                        $txt_message = '';
                        $msg = $obj2->MessageData[0]->MessageParts;
                        for ($x = 0; $x < count($msg); $x++) {
                            $txt_message .=  $msg[$x]->Text;
                        }
                        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                            ->where('SMS_RESPONSE_JOB_ID',  $obj2->JobId)
                            ->update([
                                'SMS_RESPONSE_MSG_ID' => $msg[0]->MsgId,
                                'SMS_TEXT_MESSAGE' => $txt_message,
                                'SMS_CREDIT_USED' => count($msg),
                            ]);
                    }

                    if ($obj2->ErrorCode == '000') {
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

        try {

            $data_arry = array(
                'user' => "ufund_official",
                'password' => "ufund@2022",
                'msisdn' => $phone,
                'sid' => "UFUND TH",
                // 'msg' => "UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=" . $list_sendSMS[$i]->INVOICE_ID,
                // 'msg' =>  $object_type->NOTPASS,
                'msg' => $object_type->$type,
                'fl' => "0",
                'dc' => "8",
            );
            // dd($data_arry['msg']);
            list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data_arry);
            $obj2 = json_decode($content);
            // dd($obj2);
            return $obj2;
        } catch (Exception $e) {

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

        if(count($list_sendSMS) == 0){
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
            $message_sms_app = str_replace("[Approve]", ( isset($list_sendSMS[0]->APPROVE_CODE) ? $list_sendSMS[0]->APPROVE_CODE : null ) , $message_sms_contract);


            $data_arry = array(
                'user' => "ufund_official",
                'password' => "ufund@2022",
                'msisdn' => $phone,
                'sid' => "UFUND TH",
                'msg' => $message_sms_app,
                'fl' => "0",
                'dc' => "8",
            );
            // echo '<pre>';
            // print_r($list_sendSMS);
            // echo '<pre>';
            // dd($data_arry);
            // echo '<pre>';
            // print_r($data_arry);
            // echo '<pre>';

            list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data_arry);
            $obj2 = json_decode($content);

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $DUE_DATE = isset($list_sendSMS[0]->DUE_DATE) ? $list_sendSMS[0]->DUE_DATE : null;
            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $list_sendSMS[0]->QUOTATION_ID,
                'APP_ID' => $list_sendSMS[0]->APP_ID,
                'TRANSECTION_TYPE' => null,
                'TRANSECTION_ID' => null,
                'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'CONTRACT_ID' => $list_sendSMS[0]->Contract_id,
                'DUE_DATE' => $DUE_DATE,
            ]);

            if (isset($obj2->MessageData)) {
                $txt_message = '';
                $msg = $obj2->MessageData[0]->MessageParts;
                for ($x = 0; $x < count($msg); $x++) {
                    $txt_message .=  $msg[$x]->Text;
                }
                DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                    ->where('SMS_RESPONSE_JOB_ID',  $obj2->JobId)
                    ->update([
                        'SMS_RESPONSE_MSG_ID' => $msg[0]->MsgId,
                        'SMS_TEXT_MESSAGE' => $txt_message,
                        'SMS_CREDIT_USED' => count($msg),
                    ]);
            }

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
}
