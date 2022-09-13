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
use Illuminate\Http\Client\ConnectionException;

class Job_QueueCheckSMSDeliver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $SMSData;

    public $tries = 2;
    public $backoff = 5 * 60;

    public function __construct($SMSData)
    {
        $this->SMSData = $SMSData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $SMS = $this->SMSData;
        $this->CheckDeliver($SMS);
        sleep(1);
    }

    public function CheckDeliver($SMS)
    {

        // $response = Http::get('http://sms.mailbit.co.th/vendorsms/checkdelivery.aspx?user='.ENV('MAILBIT_USER').'&password='.ENV('MAILBIT_PASS').'&messageid=' . $SMS->SMS_RESPONSE_MSG_ID);

        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->get('https://api.send-sms.in.th/api/v2/MessageStatus', [
            "ApiKey" => ENV('MAILBIT_APIKey'),
            "ClientId" => ENV('MAILBIT_ClientID'),
            "MessageId" => $SMS->SMS_RESPONSE_MSG_ID,
        ]);

        $res = json_decode($response->body());

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');
        // dd($res->ErrorCode);
        if($res->ErrorCode != 0){
            return false;
        }
        // dd($res->ErrorCode);
        // dd($res->Data->Status);
        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->where('SMS_ID', $SMS->SMS_ID)
            ->update([
                'SMS_Status_Delivery' => $res->Data->Status,
                // 'status' => $status
            ]);
    }
}
