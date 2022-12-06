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

class API_SCB_Bill_H2H extends BaseController
{

    // Production
    public static $API_Key;
    public static $API_Secret;
    public static $SCB_BILLER_ID;

    public function __construct()
    {
        self::$API_Key = config('global_variable.SCB_API_Key');
        self::$API_Secret = config('global_variable.SCB_API_Secret');

        self::$SCB_BILLER_ID = config('global_variable.SCB_BILLER_ID');
    }

    public function Test()
    {
        return self::$API_Key;
    }


    private function Code_errorMsg($Code)
    {
        $collection = collect([
            // Invalid input data group
            [
                'Code'      =>  '1000',
                'message'   =>  'Invalid data',
            ],
            [
                'Code'      =>  '1001',
                'message'   =>  'Invalid reference1',
            ],
            [
                'Code'      =>  '1002',
                'message'   =>  'Invalid reference2',
            ],
            [
                'Code'      =>  '1003',
                'message'   =>  'Invalid reference3',
            ],
            [
                'Code'      =>  '1004',
                'message'   =>  'Invalid amount',
            ],

            // Unable to process group
            [
                'Code'      =>  '2000',
                'message'   =>  'Unable to process transaction',
            ],
            [
                'Code'      =>  '2001',
                'message'   =>  'Duplicate transaction',
            ],
            [
                'Code'      =>  '2002',
                'message'   =>  'Over due',
            ],

            // System error group
            [
                'Code'      =>  '9000',
                'message'   =>  'System error',
            ],
            [
                'Code'      =>  '9001',
                'message'   =>  'System is busy',
            ],
            [
                'Code'      =>  '9002',
                'message'   =>  'Time out',
            ],
        ]);

        $filteredCollection = $collection->where('Code', '==', $Code)->values();

        // dd($filteredCollection);

        return $filteredCollection;
    }


    public function SCB_Routing(Request $request)
    {

        try {

            $data = $request->all();

            if(isset($data['request']) == false) throw new Exception('1000');

            if ($data['request'] == 'verify') {
                $returnData = $this->Payment_Verify($data);
            } elseif ($data['request'] == 'confirm') {
                $returnData = $this->Payment_confirmation($data);
            } elseif ($data['request'] == 'cancel') {
                $returnData = $this->Payment_Cancel($data);
            } else {
                throw new Exception('1000');
            }

            return $returnData;
        } catch (Exception $e) {

            $Msg_Error = $this->Code_errorMsg($e->getMessage());
            // dd($Msg_Error);

            return response()->json(array(
                "response" => isset($data['request']) ? $data['request'] : null,
                "resCode" => isset($Msg_Error[0]['Code']) ? $Msg_Error[0]['Code'] : 9000,
                "resMesg" => isset($Msg_Error[0]['message']) ? $Msg_Error[0]['message'] : 'System error',
                "tranID" => isset($data['tranID']) ? $data['tranID'] : null,
                "reference2" => isset($data['reference2']) ? $data['reference2'] : null,
                "paymentID" => self::$SCB_BILLER_ID
            ));
        }
    }


    private function Insert_Request($data)
    {
        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();

        try {
            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SCB_BILLPAYMENT')->insert([
                'type_request' => $data['request'],
                'user' => isset($data['user']) ? $data['user'] : null,
                'password' => isset($data['password']) ? $data['password'] : null,
                'tranID' => $data['tranID'],
                'tranDate' => $data['tranDate'],
                'channel' => $data['channel'],
                'account' => $data['account'],
                'amount' => $data['amount'],
                'reference1' => $data['reference1'],
                'reference2' => isset($data['reference2']) ? $data['reference2'] : null,
                'reference3' => isset($data['reference3']) ? $data['reference3'] : null,
                'branchCode' => isset($data['branchCode']) ? $data['branchCode'] : null,
                'terminalID' => isset($data['terminalID']) ? $data['terminalID'] : null,
                'CREATE_DATE' => $dateNow,
                'CREATE_BY' => 'SCB API',
            ]);
        } catch (Exception $e) {
            return (throw new Exception('2000'));
        }
    }


    private function Check_ref($data)
    {
        $DB_TTP_InvBarcode_Ref01 = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
            ->select('SEQ_ID', 'INV_NO', 'REF1_NO', 'REF2_NO', 'INV_AMT', 'CUST_CARD_ID')
            ->where('REF1_NO', $data['reference1'])
            ->get();
        if (count($DB_TTP_InvBarcode_Ref01) == 0) return (throw new Exception('1001'));

        $DB_TTP_InvBarcode_Ref02 = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
            ->select('SEQ_ID', 'INV_NO', 'REF1_NO', 'REF2_NO', 'INV_AMT', 'CUST_CARD_ID')
            ->where('REF1_NO', $data['reference1'])
            ->where('REF2_NO', $data['reference2'])
            ->get();
        if (count($DB_TTP_InvBarcode_Ref02) == 0) return (throw new Exception('1002'));

        // $Amount = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
        //     ->select('TTP_INV_BARCODE.INV_NO',   'TTP_INV_BARCODE.REF1_NO', 'TTP_INV_BARCODE.REF2_NO', 'TTP_INV_BARCODE.INV_AMT', 'TTP_APPL_TRANS.PREMIUM_AMT')
        //     // ->leftJoin('TTP_APPL_TRANS', 'TTP_INV_BARCODE.REF1_NO', '=', 'TTP_APPL_TRANS.PAYMENT_REF1')
        //     ->leftJoin('TTP_APPL_TRANS', function ($join) {
        //         $join->on('TTP_INV_BARCODE.REF1_NO', 'TTP_APPL_TRANS.PAYMENT_REF1');
        //         $join->on('TTP_INV_BARCODE.REF2_NO', 'TTP_APPL_TRANS.PAYMENT_REF2');
        //     })
        //     ->where('TTP_INV_BARCODE.REF1_NO', $data['reference1'])
        //     ->where('TTP_INV_BARCODE.REF2_NO', $data['reference2'])
        //     ->where('TTP_APPL_TRANS.PREMIUM_AMT', $data['amount'])
        //     ->get();
        // if (count($Amount) == 0) return throw new Exception('1004');
    }


    private function Payment_Verify($data)
    {

        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();

        try {

            $this->Check_ref($data);

            $this->Insert_Request($data);

            return response()->json(array(
                "response" => "verify",
                "resCode" => "0000",
                "resMesg" => "Success",
                "tranID" => $data['tranID'],
                "reference2" => $data['reference2'],
                "paymentID" => self::$SCB_BILLER_ID
            ));
        } catch (Exception $e) {

            $Msg_Error = $this->Code_errorMsg($e->getMessage());

            return response()->json(array(
                "response" => isset($data['request']) ? $data['request'] : null,
                "resCode" => isset($Msg_Error[0]['Code']) ? $Msg_Error[0]['Code'] : 2000,
                "resMesg" => isset($Msg_Error[0]['message']) ? $Msg_Error[0]['message'] : 'Unable to process transaction',
                "tranID" => isset($data['tranID']) ? $data['tranID'] : null,
                "reference2" => isset($data['reference2']) ? $data['reference2'] : null,
                "paymentID" => self::$SCB_BILLER_ID
            ));
        }
    }

    private function Payment_confirmation($data)
    {
        // ธนาคารส่ง request มาเพื่อแจ้งผลการชำระของลูกค้าว่าเรียบร้อยแล้วให้ทราบ
        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();

        try {

            // $this->Check_ref($data);

            $this->Insert_Request($data);

            return response()->json(array(
                "response" => "confirm",
                "resCode" => "0000",
                "resMesg" => "Success",
                "tranID" => $data['tranID'],
                "reference2" => $data['reference2'],
                "paymentID" => self::$SCB_BILLER_ID
            ));
        } catch (Exception $e) {

            $Msg_Error = $this->Code_errorMsg($e->getMessage());

            // dd($Msg_Error[0]['message']);

            return response()->json(array(
                "response" => isset($data['request']) ? $data['request'] : null,
                "resCode" => isset($Msg_Error[0]['Code']) ? $Msg_Error[0]['Code'] : 2000,
                "resMesg" => isset($Msg_Error[0]['message']) ? $Msg_Error[0]['message'] : 'Unable to process transaction',
                "tranID" => isset($data['tranID']) ? $data['tranID'] : null,
                "reference2" => isset($data['reference2']) ? $data['reference2'] : '',
                "paymentID" => self::$SCB_BILLER_ID
            ));
        }
    }

    private function Payment_Cancel($data)
    {
        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();

        try {

            $this->Check_ref($data);

            $this->Insert_Request($data);

            return response()->json(array(
                "response" => "cancel",
                "resCode" => "0000",
                "resMesg" => "Success",
                "tranID" => $data['tranID'],
                "reference2" => $data['reference2'],
                "paymentID" => self::$SCB_BILLER_ID
            ));
        } catch (Exception $e) {

            $Msg_Error = $this->Code_errorMsg($e->getMessage());
            // dd($Msg_Error[0]['message']);

            return response()->json(array(
                "response" => isset($data['request']) ? $data['request'] : null,
                "resCode" => isset($Msg_Error[0]['Code']) ? $Msg_Error[0]['Code'] : 2000,
                "resMesg" => isset($Msg_Error[0]['message']) ? $Msg_Error[0]['message'] : 'Unable to process transaction',
                "tranID" => isset($data['tranID']) ? $data['tranID'] : null,
                "reference2" => isset($data['reference2']) ? $data['reference2'] : '',
                "paymentID" => self::$SCB_BILLER_ID
            ));
        }
    }
}
