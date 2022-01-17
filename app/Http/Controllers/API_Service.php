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

class API_Service extends BaseController
{
    // public function Send_SMS()
    // {
    //     try {
    //         $dataNum = ['04512', '012', '1515'];
    //         $response = Http::get('http://ufund-portal.webhop.biz:9090/API-Corelease/api/master_prefix', [
    //             'name' => 'Taylor,456',
    //             'page' => '',
    //         ]);
    //         dd($response);
    //         // return $response;
    //     } catch (Exception $e) {
    //         return response()->json(array(
    //             'status' => 'Error',
    //             'message' => $e->getMessage()
    //         ));
    //     }
    // }


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
            // dd($data['number']);
            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');
            $timestamp = date('H:i:s');
            $inv_date = $data['INV_DATE'];

            $list_sendSMS = DB::select(DB::raw("exec SP_Get_Invoice_SMS  @DateInput = '" . $inv_date . "' "));

            for ($i = 0; $i < count($list_sendSMS); $i++) {

                try {

                    $phone = '66' . mb_substr($list_sendSMS[$i]->PHONE, 1);

                    // $data = $this->submit_send_SMS($data_post);
                    // $list_sendSMS = DB::select(DB::raw("exec SP_Get_Invoice_SMS  @DateInput = '2022-01-01' "));
                    // return $MT;

                    $data = array(
                        'user' => "ufund_official",
                        'password' => "ufund@2022",
                        'msisdn' => $phone,
                        'sid' => "UFUND TH",
                        'msg' => "UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=" . $list_sendSMS[$i]->INVOICE_ID,
                        'fl' => "0",
                        'dc' => "8",
                    );

                    list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data);
                    $obj2 = json_decode($content);

                    $new_id = DB::table('dbo.LOG_SEND_SMS')
                        ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                        ->where('date', $dateNow)
                        ->get();

                    DB::table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                        'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $dateNow,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->inv_date,
                    ]);

                    // dd(count($obj2->MessageData[$i]->MessageParts));
                    // print_r($obj2);
                    if ($obj2->MessageData != null) {
                        $msg = $obj2->MessageData[0]->MessageParts;
                        for ($x = 0; $x < count($msg); $x++) {
                            // dd($obj2->MessageData[$i]->Number);
                            DB::table('dbo.LOG_SEND_SMS_DETAIL')->insert([
                                'SMS_JobID' => $obj2->JobId,
                                'SMS_Msg_ID' => $msg[$x]->MsgId,
                                'SMS_Number' => $obj2->MessageData[0]->Number,
                                'SMS_Part_ID' => $msg[$x]->PartId,
                                'SMS_Text' => $msg[$x]->Text,
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    date_default_timezone_set('Asia/bangkok');
                    $dateNow = date('Y-m-d');
                    $timestamp = date('H:i:s');
                    $new_error_id = date("Ymdhis");
                    DB::table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => '000000',
                        'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                        'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                        'SEND_DATE' => $dateNow,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->inv_date,
                    ]);

                    DB::table('dbo.LOG_SEND_SMS_DETAIL')->insert([
                        'SMS_JobID' => 'ERROR-' . $new_error_id,
                        'SMS_Msg_ID' => '0X00',
                        'SMS_Number' => $phone,
                        'SMS_Part_ID' => '0X',
                        'SMS_Text' => $e->getMessage(),
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

    public function SMS_Check_Credit()
    {
        $data = array(
            'user' => "ufund_official",
            'password' => "ufund@2022",
            'sid' => "UFUND Th",
        );
        list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/CheckBalance.aspx", 'www.comseven.com', $data);
        return $content;
    }


    public function test_send_SMS(Request $request)
    {

        // $_data = new \stdClass();

        // $_data->ErrorCode = '000';
        // $_data->ErrorMessage = 'Success';
        // $_data->JobId = $i + 1;

        // $MessageParts = new \stdClass();
        // $MessageParts->MsgId  = '6680481763-A' . $i;
        // $MessageParts->PartId  = 1;
        // $MessageParts->Text  = 'Test Message MailBit';


        // $MessageParts2 = new \stdClass();
        // $MessageParts2->MsgId  = '6680481763-B' . $i;
        // $MessageParts2->PartId  = 2;
        // $MessageParts2->Text  = 'Mr.Kittisak';

        // $In_data = new \stdClass();
        // $In_data->Number = '6680481763';
        // $In_data->MessageParts =  array($MessageParts, $MessageParts2);

        // $_data->MessageData = array($In_data);

        // $data_En =  json_encode($return_data);
        // $obj2 = json_decode($data_En);

        try {

            $data = $request->all();
            $return_data = new \stdClass();
            // dd($data['number']);
            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');
            $timestamp = date('H:i:s');
            $inv_date = $data['INV_DATE'];

            $list_sendSMS = DB::select(DB::raw("exec SP_Get_Invoice_SMS  @DateInput = '" . $inv_date . "' "));

            for ($i = 0; $i < count($list_sendSMS); $i++) {
                try {

                    $phone = '66804817163';

                    // $data = $this->submit_send_SMS($data_post);
                    // $list_sendSMS = DB::select(DB::raw("exec SP_Get_Invoice_SMS  @DateInput = '2022-01-01' "));
                    // return $MT;

                    $data = array(
                        'user' => "adminufund",
                        'password' => "Comseven#sms",
                        'msisdn' => $phone,
                        'sid' => "UFUND",
                        'msg' => "UFUND จัดส่งใบแจ้งหนี้ สามารถชำระด้วย QR code บน Mobile Banking และไม่ต้องนำหลักฐานการโอนแจ้งกลับ กรุณารอใบเสร็จในระบบภายใน 7 วันทำการ คลิ๊ก https://ufund.comseven.com/Runtime/Runtime/Form/INVView/?INVOICE_ID=".$list_sendSMS[$i]->INVOICE_ID,
                        'fl' => "0",
                        'dc' => "8",
                    );

                    list($header, $content) = $this->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data);
                    $obj2 = json_decode($content);

                    $new_id = DB::table('dbo.LOG_SEND_SMS')
                        ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                        ->where('date', $dateNow)
                        ->get();

                    DB::table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                        'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                        'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                        'SEND_DATE' => $dateNow,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->DUE_DATE,
                    ]);

                    // dd(count($obj2->MessageData[$i]->MessageParts));
                    // print_r($obj2);
                    if ($obj2->MessageData != null) {
                        $msg = $obj2->MessageData[0]->MessageParts;
                        for ($x = 0; $x < count($msg); $x++) {
                            // dd($obj2->MessageData[$i]->Number);
                            DB::table('dbo.LOG_SEND_SMS_DETAIL')->insert([
                                'SMS_JobID' => $obj2->JobId,
                                'SMS_Msg_ID' => $msg[$x]->MsgId,
                                'SMS_Number' => $obj2->MessageData[0]->Number,
                                'SMS_Part_ID' => $msg[$x]->PartId,
                                'SMS_Text' => $msg[$x]->Text,
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    date_default_timezone_set('Asia/bangkok');
                    $dateNow = date('Y-m-d');
                    $timestamp = date('H:i:s');
                    $new_error_id = date("Ymdhis");
                    DB::table('dbo.LOG_SEND_SMS')->insert([
                        'DATE' => $dateNow,
                        'RUNNING_NO' => $new_id[0]->new_id,
                        'QUOTATION_ID' => $list_sendSMS[$i]->QUOTATION_ID,
                        'APP_ID' => $list_sendSMS[$i]->APP_ID,
                        'TRANSECTION_TYPE' => 'INVOICE',
                        'TRANSECTION_ID' => $list_sendSMS[$i]->INVOICE_ID,
                        'SMS_RESPONSE_CODE' => '000000',
                        'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                        'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                        'SEND_DATE' => $dateNow,
                        'SEND_TIME' => $timestamp,
                        'SEND_Phone' => $phone,
                        'CONTRACT_ID' => $list_sendSMS[$i]->CONTRACT_ID,
                        'DUE_DATE' => $list_sendSMS[$i]->DUE_DATE,
                    ]);

                    DB::table('dbo.LOG_SEND_SMS_DETAIL')->insert([
                        'SMS_JobID' => 'ERROR-' . $new_error_id,
                        'SMS_Msg_ID' => '0X00',
                        'SMS_Number' => $phone,
                        'SMS_Part_ID' => '0X',
                        'SMS_Text' => $e->getMessage(),
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
}
