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

        $response = Http::get('http://sms.mailbit.co.th/vendorsms/checkdelivery.aspx?user=ufund_official&password=ufund@2022&messageid=' . $SMS->SMS_RESPONSE_MSG_ID);

        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->where('SMS_ID', $SMS->SMS_ID)
            ->update([
                'SMS_Status_Delivery' => $response,
                // 'status' => $status
            ]);
    }
}
