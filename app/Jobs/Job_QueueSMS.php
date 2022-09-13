<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Jobs\Job_QueueInsertSMS;

class Job_QueueSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $customer;

    public $tries = 2;
    public $backoff = 5 * 60;

    public static $obj2;
    public static $phone;

    
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer = $this->customer;
        $this->Send_SMS($customer);
        sleep(1);
    }


    public function PostRequest_SMS($url, $_data)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->post($url, $_data);
        $resData =  $response->body();

        return array($resData, $_data['message']);
    }


    public function Send_SMS($cus_data)
    {

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');

        $phone = '66' . mb_substr($cus_data->PHONE, 1);
        // $phone = '66804817163';
        self::$phone = $phone;

        $split_str = explode("-", $cus_data->DUE_DATE);

        $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
        $strMonthThai = $strMonthCut[(int)$split_str[1]];

        $year = substr(($split_str[0] + 543), -2);

        $textDate = $split_str[2] . " " . $strMonthThai . " " . $year;
        // dd($textDate);
        // printf();
        // $data_arry = array(
        //     'user' => ENV('MAILBIT_USER'),
        //     'password' => ENV('MAILBIT_PASS'),
        //     'msisdn' => $phone,
        //     'sid' => ENV('MAILBIT_sid'),
        //     'msg' => "UFUND ส่งบิล รอบกำหนดชำระ " . $textDate . " กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก " . $cus_data->SHT_INV_URL . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
        //     'fl' => "0",
        //     'dc' => "8",
        // );

        $data_arry = array(
            'apiKey' => ENV('MAILBIT_APIKey'),
            'clientId' => ENV('MAILBIT_ClientID'),
            'mobileNumbers' => $phone,
            'SenderId' => ENV('MAILBIT_SenderId'),
            'message' => "UFUND ส่งบิล รอบกำหนดชำระ " . $textDate . " กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก " . $cus_data->SHT_INV_URL . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
            'is_Unicode' => true,
            'is_Flash' => false,
        );

        list($content, $txt_message) = $this->PostRequest_SMS("https://api.send-sms.in.th/api/v2/SendSMS", $data_arry);

        $obj2 = json_decode($content);
        self::$obj2 = $obj2;
        Job_QueueInsertSMS::dispatch($obj2, $cus_data, $phone, $txt_message)->onQueue('site_sms_insert');
    }


    public function failed($e)
    {

        $cus_data = $this->customer;

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');
        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');
        $new_error_id = date("Ymdhis");

        $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            ->where('date', $dateNow)
            ->get();

        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
            'DATE' => $dateNow,
            'RUNNING_NO' => $new_id[0]->new_id,
            'QUOTATION_ID' => $cus_data->QUOTATION_ID,
            'APP_ID' => $cus_data->APP_ID,
            'TRANSECTION_TYPE' => 'INVOICE',
            'TRANSECTION_ID' => $cus_data->INVOICE_ID,
            'SMS_RESPONSE_CODE' => '0x00',
            'SMS_RESPONSE_MESSAGE' => 'API ERROR',
            'SMS_RESPONSE_JOB_ID' => 'ERROR' . $new_error_id,
            'SEND_DATE' => $datestamp,
            'SEND_TIME' => $timestamp,
            'SEND_Phone' => self::$phone,
            'CONTRACT_ID' => $cus_data->CONTRACT_ID,
            'DUE_DATE' => $cus_data->DUE_DATE,
            'SMS_TEXT_MESSAGE' => $e->getMessage(),
        ]);
    }
}
