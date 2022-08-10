<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Validator;

use Webpatser\Uuid\Uuid;

use App\Http\Controllers\API_Service_SMS;

class API_Sandbox_SCB extends BaseController
{

    private $API_Key = 'l7c710011771dd47d7b68998a7ce942f5e';
    private $API_Secret = '4c726a6a58b14bd38f46d4aebab794d6';


    public function SCB_OauthToken()
    {
        try {

            $token_uuid = Uuid::generate(4)->string;
            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'requestUId' => $token_uuid,
                // 'requestUId' => '85230887-e643-4fa4-84b2-4e56709c4ac4',
                'resourceOwnerId' => $this->API_Key,
                'accept-language' => 'EN',
            ])->post('https://api-sandbox.partners.scb/partners/sandbox/v1/oauth/token', [
                "applicationKey" => $this->API_Key,
                "applicationSecret" => $this->API_Secret,
            ]);

            $oauthSCB = json_decode($response->body());
            // dd($oauth->status->code);

            return $oauthSCB;

        } catch (ConnectionException $e) {
            return $e;
        }
    }


    public function SCB_Create_QR_Code(Request $request)
    {

        try {

            $data = $request->all();

            date_default_timezone_set('Asia/bangkok');
            $dateNow = Carbon::now();

            $uuid = Uuid::generate(4, $data['SEQ_ID'] . $dateNow)->string;

            $token = $this->SCB_OauthToken();

            if ($token->status->code != '1000') {
                return response()->json(array(
                    'Code' => $token->status->code,
                    'Status' => 'Connect SCB Error',
                    'message' => $token
                ));
            }

            // dd($token);

            $DB_APPL_TRANS = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_APPL_TRANS')
                ->select('SEQ_ID', 'PAYMENT_REF1', 'MOBILE_NO', 'PREMIUM_AMT', 'APPL_NO')
                ->where('SEQ_ID', $data['SEQ_ID'])
                ->get();

            // dd(count($DB_DATA));
            if (count($DB_APPL_TRANS) == 0) {
                return response()->json(array(
                    'Code' => '2100',
                    'Status' => 'Data Not Found',
                ));
            }

            $ref2 = '22'.Carbon::now()->timestamp;

            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'accept-language' => 'EN',
                'authorization' => 'Bearer ' . $token->data->accessToken,
                'requestUId' => $uuid,
                // 'requestUId' => '1b01dff2-b3a3-4567-adde-cd9dd73c8b6d',
                'resourceOwnerId' => $this->API_Key
            ])->post('https://api-sandbox.partners.scb/partners/sandbox/v1/payment/qrcode/create', [
                "qrType" => "PP",
                "ppType" => "BILLERID",
                "ppId" => "526446253766021",
                "amount" => $DB_APPL_TRANS[0]->PREMIUM_AMT,
                // "amount" => "1.00",
                "ref1" => $DB_APPL_TRANS[0]->PAYMENT_REF1,
                "ref2" => $ref2,
                "ref3" => "IYV"
            ]);

            $res_data = json_decode($response->body());
            
            if ($res_data->status->code != '1000') {
                throw new Exception($res_data->status->description);
            }
            

            $DB_QRD_ID = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_QR_DOWN')->insertGetId([
                // 'SEQ_ID' => $SEQ_ID,
                'PAYMENT_REF1' => $DB_APPL_TRANS[0]->PAYMENT_REF1,
                'PAYMENT_REF2' => $ref2,
                'CREATE_BY' => 'API',
                'CREATE_DATE' => $dateNow,
                'QR_PAY_DOWN' => '<file><name>QR_Down_' . $DB_APPL_TRANS[0]->PAYMENT_REF1 . '</name><content>' . $res_data->data->qrImage . '</content></file>',
                'UUID' => $uuid,
            ]);

            // return $res_data;

            // dd($SEQ_ID);
            // dd($res_data);

            $phone = '66' . mb_substr($DB_APPL_TRANS[0]->MOBILE_NO, 1);
            // $phone = '66804817163';

            $API_Service_SMS = new API_Service_SMS;

            $msg_send = "กรุณาชำระเงินดาวน์ จำนวน " . $DB_APPL_TRANS[0]->PREMIUM_AMT . " บาท ผ่าน QR code บน Mobile Banking App ดาวน์โหลดที่ : https://43.254.133.148/Runtime/Runtime/Form/QRDownPayment/?QRD_ID=" . $DB_QRD_ID;
            $data_arry = array(
                'user' => "ufund_official",
                'password' => "ufund@2022",
                'msisdn' => $phone,
                'sid' => "UFUND TH",
                'msg' => $msg_send,
                'fl' => "0",
                'dc' => "8",
            );

            list($header, $content) = $API_Service_SMS->PostRequest_SMS("http://sms.mailbit.co.th/vendorsms/pushsms.aspx", 'www.comseven.com', $data_arry);
            $obj2 = json_decode($content);
            // dd($obj2);
            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $DB_CustomerData = DB::connection('sqlsrv_HPCOM7')->table('dbo.APPLICATION')
                ->select('APPLICATION.QUOTATION_ID', 'APPLICATION.APP_ID', 'CONTRACT.CONTRACT_ID')
                ->leftJoin('CONTRACT', 'CONTRACT.APPLICATION_NUMBER', '=', 'APPLICATION.APPLICATION_NUMBER')
                ->where('APPLICATION.APPLICATION_NUMBER', $DB_APPL_TRANS[0]->APPL_NO)
                ->get();

            $new_SendSMS_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
                ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                ->where('date', $dateNow)
                ->get();

            try {

                DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                    'DATE' => $dateNow,
                    'RUNNING_NO' => $new_SendSMS_id[0]->new_id,
                    'QUOTATION_ID' => $DB_CustomerData[0]->QUOTATION_ID,
                    'APP_ID' => $DB_CustomerData[0]->APP_ID,
                    'TRANSECTION_TYPE' => 'DownPayment',
                    'TRANSECTION_ID' => 'QRDID_' . $DB_QRD_ID,
                    'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                    'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                    'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                    'SEND_DATE' => $datestamp,
                    'SEND_TIME' => $timestamp,
                    'SEND_Phone' => $phone,
                    'CONTRACT_ID' => $DB_CustomerData[0]->CONTRACT_ID,
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


                DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_SMS_RESULT')->insert([
                    'SEQ_ID' => $DB_APPL_TRANS[0]->SEQ_ID,
                    'SEND_DATE' => $datestamp,
                    'SEND_TIME' => $timestamp,
                    'REF_NO1' => $DB_APPL_TRANS[0]->PAYMENT_REF1,
                    'REF_NO2' => '00',
                    'PAY_AMT' => $DB_APPL_TRANS[0]->PREMIUM_AMT,
                    'MOBILE_NO' => $DB_APPL_TRANS[0]->MOBILE_NO,
                    'SEND_STATUS' => 'success',
                    'SEND_RESULT' => 'd5906a26da074ce79a1118c3259a861e',
                    'SEND_MSG' => $msg_send,
                ]);

            } catch (\Exception $e) {
                $datestamp = date('Y-m-d');
                $timestamp = date('H:i:s');
                $new_error_id = date("Ymdhis");

                DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                    'DATE' => $dateNow,
                    'RUNNING_NO' => $new_SendSMS_id[0]->new_id,
                    'QUOTATION_ID' => $DB_CustomerData[0]->QUOTATION_ID,
                    'APP_ID' => $DB_CustomerData[0]->APP_ID,
                    'TRANSECTION_TYPE' => 'DownPayment',
                    'TRANSECTION_ID' => 'QRDID_' . $DB_QRD_ID,
                    'SMS_RESPONSE_CODE' => '0x00',
                    'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                    'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                    'SEND_DATE' => $datestamp,
                    'SEND_TIME' => $timestamp,
                    'SEND_Phone' => $phone,
                    'CONTRACT_ID' => $DB_CustomerData[0]->CONTRACT_ID,
                    'SMS_TEXT_MESSAGE' => $e->getMessage(),
                ]);
            }

            return $res_data;
        } catch (Exception $e) {

            return response()->json(array(
                'Code' => '0400',
                'Status' => 'Connect SCB Error',
                'Message' => $e->getMessage(),
            ));
        }
    }


    public function SCB_Callback_Payment_Confirm(Request $request)
    {

        $data = $request->all();

        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();
        // dd($dateNow->format('Y-m-d H:i:s'));

        try {

            $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SCB_DOWNPAYMENT')
                ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
                ->whereDate('CREATE_DATE', $dateNow->format('Y-m-d'))
                ->get();

            // DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SCB_DOWNPAYMENT')->insert([
            //     'RUNNING_NO' => $new_id[0]->new_id,
            //     'PAYMENT_REF1' => $DB_APPL_TRANS[0]->PAYMENT_REF1,
            //     'SEQ_ID' => $DB_SEQ_ID,
            //     'CREATE_DATE' => $dateNow,
            //     'CREATE_BY' => 'API',
            //     'UUID' => $uuid,
            // ]);

            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SCB_DOWNPAYMENT')
                // ->where('PAYMENT_REF1',  $data['billPaymentRef1'])
                ->insert([
                    'DATE' => $dateNow,
                    'RUNNING_NO' => $new_id[0]->new_id,
                    'PAYMENT_REF1' => $data['billPaymentRef1'],
                    'payeeProxyId' => $data['payeeProxyId'],
                    'payeeProxyType' => $data['payeeProxyType'],
                    'payeeAccountNumber' => isset($data['payeeAccountNumber']) ? $data['payeeAccountNumber'] : null,
                    'payerAccountNumber' => isset($data['payerAccountNumber']) ? $data['payerAccountNumber'] : null,
                    'payerAccountName' => isset($data['payerAccountName']) ? $data['payerAccountName'] : null,
                    'payerName' => isset($data['payerName']) ? $data['payerName'] : null,
                    'sendingBankCode' => isset($data['sendingBankCode']) ? $data['sendingBankCode'] : null,
                    'receivingBankCode' => isset($data['receivingBankCode']) ? $data['receivingBankCode'] : null,
                    'amount' => $data['amount'],
                    'transactionId' => $data['transactionId'],
                    'transactionDateandTime' => $data['transactionDateandTime'],
                    'billPaymentRef1' => $data['billPaymentRef1'],
                    'billPaymentRef2' => isset($data['billPaymentRef2']) ? $data['billPaymentRef2'] : null,
                    'billPaymentRef3' => isset($data['billPaymentRef3']) ? $data['billPaymentRef3'] : null,
                    'currencyCode' => isset($data['currencyCode']) ? $data['currencyCode'] : null,
                    'channelCode' => isset($data['channelCode']) ? $data['channelCode'] : null,
                    'transactionType' => isset($data['transactionType']) ? $data['transactionType'] : null,
                    'CREATE_DATE' => $dateNow,
                    'CREATE_BY' => 'SCB API',
                ]);


            // DB::connection('sqlsrv_HPCOM7')->table('dbo.REPAYMENT')->insert([
            //     'DATE' => $dateNow,
            //     'APPLICATION_NUMBER' => $data['billPaymentRef1'],
            //     'PAY_SUM_AMT' => $data['amount'],
            //     'UPDATE_DATE' => $dateNow,
            //     'NAME_MAKE' => 'API',
            // ]);

            DB::connection('sqlsrv_HPCOM7')->update(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_INSERT_REPAY_DOWN  @APPLICATION_NUMBER_INPUT = '" . $data['billPaymentRef1'] . "', @PAY_SUM_AMT_INPUT='" . $data['amount'] . "' "));

            return response()->json(array(
                'resCode' => '00',
                'resDesc' => 'success',
                'transactionId' => $data['transactionId'],
                // 'confirmId' => $data['confirmId']
            ));
        } catch (Exception $e) {

            return response()->json(array(
                'resCode' => '01',
                'resDesc' => 'failed',
                'transactionId' => $data['transactionId'],
                // 'confirmId' => $data['confirmId'],
                'Message' => $e->getMessage(),
            ));
        }
    }


    public function SCB_Check_slip(Request $request)
    {

        try {

            $data = $request->all();
            $token = $this->SCB_OauthToken();
            if ($token->status->code != '1000') {
                return response()->json(array(
                    'Code' => '0400',
                    'Status' => 'Server Erorr',
                    'message' => $token
                ));
            }

            dd($token);
            // $DB_SCB_LOG = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SCB_DOWNPAYMENT')
            //     ->select('PAYMENT_ID', 'transactionId', 'CONTRACT.CONTRACT_ID')
            //     ->where('APPLICATION.APPLICATION_NUMBER', $DB_APPL_TRANS[0]->APPL_NO)
            //     ->get();


            $response = Http::withHeaders([
                'accept-language' => 'EN',
                'authorization' => 'Bearer ' . $token->data->accessToken,
                'requestUId' => '19d27806-305a-4645-9746-537cf0f85865',
                'resourceOwnerId' => $this->API_Key,
            ])->get('https://api-uat.partners.scb/partners/v1/payment/billpayment/transactions/' . $data['transectionId'] . '?sendingBank=014');

            // dd($response->body());
            $res_data = json_decode($response->body());
            return $res_data;
        } catch (Exception $e) {
            // return $e->getMessage();
            return response()->json(array(
                'Code' => '0400',
                'Status' => 'Connect SCB Error',
                'Message' => $e->getMessage(),
            ));
        }
    }


    public function SCB_Payment_Transaction_Inquiry(Request $request)
    {

        try {

            $data = $request->all();
            $token = $this->SCB_OauthToken();
            if ($token->status->code != '1000') {
                return response()->json(array(
                    'Code' => '0400',
                    'Status' => 'Server Erorr',
                    'message' => $token
                ));
            }
            // dd($token);

            $DB_TTP_QRDown = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_QR_DOWN')
                ->select('QRD_ID', 'PAYMENT_REF1', 'PAYMENT_REF2', 'UUID', 'CREATE_DATE')
                ->where('QRD_ID', $data['QRDown_QrdId'])
                ->get();


            if (count($DB_TTP_QRDown) == 0) {
                return response()->json(array(
                    'Code' => '2100',
                    'Status' => 'Data Not Found',
                ));
            }


            $billerId = '526446253766021';
            // transactionDate={YYYY-MM-DD}
            $transactionDate = Carbon::parse($DB_TTP_QRDown[0]->CREATE_DATE)->format('Y-m-d');
            $ref1 = $DB_TTP_QRDown[0]->PAYMENT_REF1;
            $ref2 = $DB_TTP_QRDown[0]->PAYMENT_REF2;
            $UUID = $DB_TTP_QRDown[0]->UUID;

            $response = Http::withHeaders([
                'accept-language' => 'EN',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer ' . $token->data->accessToken,
                // 'requestUId' => '871872a7-ed08-4229-a637-bb7c733305db',
                'requestUId' => $UUID,
                'resourceOwnerId' => $this->API_Key,
            ])->get('https://api-sandbox.partners.scb/partners/sandbox/v1/payment/billpayment/inquiry?eventCode=00300100&billerId='.$billerId.'&reference1='.$ref1.'&reference2='.$ref2.'&transactionDate='.$transactionDate);

            // dd($response->body());
            $res_data = json_decode($response->body());
            return $res_data;
        } catch (Exception $e) {
            // return $e->getMessage();
            return response()->json(array(
                'Code' => '0400',
                'Status' => 'Connect SCB Error',
                'Message' => $e->getMessage(),
            ));
        }
    }
}