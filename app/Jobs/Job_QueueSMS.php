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

class Job_QueueSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $customer;

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


    public function PostRequest_SMS($url, $referer, $_data)
    {
        // // convert variables array to string:
        $data = array();
        foreach ($_data as $key => $value) {
            // echo $key;
            $data[] = "$key=$value";
        }
        $data = implode('&', $data);
        // format --> test1=a&test2=b etc.
        // parse the given URL
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            die('Only HTTP request are supported !');
        }
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


    public function Send_SMS($cus_data)
    {

        try {

            date_default_timezone_set('Asia/bangkok');
            $dateNow = date('Y-m-d');

            $datestamp = date('Y-m-d');
            $timestamp = date('H:i:s');

            $phone = '66' . mb_substr($cus_data->PHONE, 1);
            // $phone = '66804817163';

            $split_str = explode("-", $cus_data->DUE_DATE);

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
                'msg' => "UFUND ส่งบิล รอบกำหนดชำระ " . $textDate . " กรุณาชำระ ภายใน 22:00น. เพื่อหลีกเลี่ยงค่าปรับ คลิ๊ก " . $cus_data->SHT_INV_URL . " เพื่อดูรายละเอียดบิล หากชำระแล้วใบเสร็จจะออกให้ภายใน 7-10 วันทำการ",
                'fl' => "0",
                'dc' => "8",
            );

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
                'QUOTATION_ID' => $cus_data->QUOTATION_ID,
                'APP_ID' => $cus_data->APP_ID,
                'TRANSECTION_TYPE' => 'INVOICE',
                'TRANSECTION_ID' => $cus_data->INVOICE_ID,
                'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
                'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
                'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'CONTRACT_ID' => $cus_data->CONTRACT_ID,
                'DUE_DATE' => $cus_data->DUE_DATE,
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
            
            DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
                'DATE' => $dateNow,
                'RUNNING_NO' => $new_id[0]->new_id,
                'QUOTATION_ID' => $cus_data->QUOTATION_ID,
                'APP_ID' => $cus_data->APP_ID,
                'TRANSECTION_TYPE' => 'INVOICE',
                'TRANSECTION_ID' => $cus_data->INVOICE_ID,
                'SMS_RESPONSE_CODE' => '0x00',
                'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
                'SMS_RESPONSE_JOB_ID' => 'ERROR-' . $new_error_id,
                'SEND_DATE' => $datestamp,
                'SEND_TIME' => $timestamp,
                'SEND_Phone' => $phone,
                'CONTRACT_ID' => $cus_data->CONTRACT_ID,
                'DUE_DATE' => $cus_data->DUE_DATE,
                'SMS_TEXT_MESSAGE' => $e->getMessage(),
            ]);

            $return_data = new \stdClass();

            $return_data->Code = '0X0MB0000';
            $return_data->Status =  $e->getMessage();

            return $return_data;
        }
    }
}
