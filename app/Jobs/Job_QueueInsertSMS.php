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

class Job_QueueInsertSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $obj2;
    public $cus_data;
    public $phone;
    public $txt_message;

    public $tries = 2;
    public $backoff = 7 * 60;

    public function __construct($obj2, $cus_data, $phone, $txt_message)
    {
        $this->obj2 = $obj2;
        $this->cus_data = $cus_data;
        $this->phone = $phone;
        $this->txt_message = $txt_message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        $obj2 = $this->obj2;
        $cus_data = $this->cus_data;
        $phone = $this->phone;
        $txt_message = $this->txt_message;

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');

        $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            ->where('date', $dateNow)
            ->get();

        $MSG_ID = null;
        $SumCredit = null;
        if (!is_null($obj2->Data)) {
            $MSG_ID = $obj2->Data[0]->MessageId;
            $SumCredit = count($obj2->Data);
        }


        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
            'DATE' => $dateNow,
            'RUNNING_NO' => $new_id[0]->new_id,
            'QUOTATION_ID' => $cus_data->QUOTATION_ID,
            'APP_ID' => $cus_data->APP_ID,
            'TRANSECTION_TYPE' => 'INVOICE',
            'TRANSECTION_ID' => $cus_data->INVOICE_ID,
            'SMS_RESPONSE_CODE' => $obj2->ErrorCode == 0 ? '000' : $obj2->ErrorCode,
            'SMS_RESPONSE_MESSAGE' => $obj2->ErrorCode == 0 ? 'Success' : $obj2->ErrorDescription,
            // 'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
            'SEND_DATE' => $datestamp,
            'SEND_TIME' => $timestamp,
            'SEND_Phone' => $phone,
            'CONTRACT_ID' => $cus_data->CONTRACT_ID,
            'DUE_DATE' => $cus_data->DUE_DATE,
            'SMS_RESPONSE_MSG_ID' => $MSG_ID,
            'SMS_TEXT_MESSAGE' => $txt_message,
            'SMS_CREDIT_USED' => $SumCredit,
        ]);

        // if ($obj2->MessageData) {
        //     $txt_message = '';
        //     $msg = $obj2->MessageData[0]->MessageParts;
        //     for ($x = 0; $x < count($msg); $x++) {
        //         $txt_message .=  $msg[$x]->Text;
        //     }
        //     DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //         ->where('SMS_RESPONSE_JOB_ID',  $obj2->JobId)
        //         ->update([
        //             'SMS_RESPONSE_MSG_ID' => $msg[0]->MsgId,
        //             'SMS_TEXT_MESSAGE' => $txt_message,
        //             'SMS_CREDIT_USED' => count($msg),
        //         ]);
        // }
    }

    public function failed($e)
    {
        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');
        $datestamp = date('Y-m-d');
        $timestamp = date('H:i:s');
        $new_error_id = date("Ymdhis");

        $obj2 = $this->obj2;
        $cus_data = $this->cus_data;
        $phone = $this->phone;

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
            'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
            'SMS_RESPONSE_JOB_ID' => 'ERROR' . $new_error_id,
            'SEND_DATE' => $datestamp,
            'SEND_TIME' => $timestamp,
            'SEND_Phone' => $phone,
            'CONTRACT_ID' => $cus_data->CONTRACT_ID,
            'DUE_DATE' => $cus_data->DUE_DATE,
            'SMS_TEXT_MESSAGE' => $e->getMessage(),
        ]);
    }



    // public function handle()
    // {
    //     $obj2 = $this->obj2;
    //     $cus_data = $this->cus_data;
    //     $phone = $this->phone;

    //     date_default_timezone_set('Asia/bangkok');
    //     $dateNow = date('Y-m-d');

    //     $datestamp = date('Y-m-d');
    //     $timestamp = date('H:i:s');

    //     $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
    //         ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
    //         ->where('date', $dateNow)
    //         ->get();

    //     try {

    //         DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
    //             'DATE' => $dateNow,
    //             'RUNNING_NO' => $new_id[0]->new_id,
    //             'QUOTATION_ID' => $cus_data->QUOTATION_ID,
    //             'APP_ID' => $cus_data->APP_ID,
    //             'TRANSECTION_TYPE' => 'INVOICE',
    //             'TRANSECTION_ID' => $cus_data->INVOICE_ID,
    //             'SMS_RESPONSE_CODE' => $obj2->ErrorCode,
    //             'SMS_RESPONSE_MESSAGE' => $obj2->ErrorMessage,
    //             'SMS_RESPONSE_JOB_ID' => $obj2->JobId,
    //             'SEND_DATE' => $datestamp,
    //             'SEND_TIME' => $timestamp,
    //             'SEND_Phone' => $phone,
    //             'CONTRACT_ID' => $cus_data->CONTRACT_ID,
    //             'DUE_DATE' => $cus_data->DUE_DATE,
    //         ]);

    //         if ($obj2->MessageData) {
    //             $txt_message = '';
    //             $msg = $obj2->MessageData[0]->MessageParts;
    //             for ($x = 0; $x < count($msg); $x++) {
    //                 $txt_message .=  $msg[$x]->Text;
    //             }
    //             DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
    //                 ->where('SMS_RESPONSE_JOB_ID',  $obj2->JobId)
    //                 ->update([
    //                     'SMS_RESPONSE_MSG_ID' => $msg[0]->MsgId,
    //                     'SMS_TEXT_MESSAGE' => $txt_message,
    //                     'SMS_CREDIT_USED' => count($msg),
    //                 ]);
    //         }
    //     } catch (Exception $e) {

    //         date_default_timezone_set('Asia/bangkok');
    //         $datestamp = date('Y-m-d');
    //         $timestamp = date('H:i:s');
    //         $new_error_id = date("Ymdhis");

    //         DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
    //             'DATE' => $dateNow,
    //             'RUNNING_NO' => $new_id[0]->new_id,
    //             'QUOTATION_ID' => $cus_data->QUOTATION_ID,
    //             'APP_ID' => $cus_data->APP_ID,
    //             'TRANSECTION_TYPE' => 'INVOICE',
    //             'TRANSECTION_ID' => $cus_data->INVOICE_ID,
    //             'SMS_RESPONSE_CODE' => '0x00',
    //             'SMS_RESPONSE_MESSAGE' => 'UFUND SYSTEM ERROR',
    //             'SMS_RESPONSE_JOB_ID' => 'ERROR' . $new_error_id,
    //             'SEND_DATE' => $datestamp,
    //             'SEND_TIME' => $timestamp,
    //             'SEND_Phone' => $phone,
    //             'CONTRACT_ID' => $cus_data->CONTRACT_ID,
    //             'DUE_DATE' => $cus_data->DUE_DATE,
    //             'SMS_TEXT_MESSAGE' => $e->getMessage(),
    //         ]);
    //     }
    // }
}
