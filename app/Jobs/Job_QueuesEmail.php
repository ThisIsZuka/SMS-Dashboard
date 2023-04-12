<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Jobs\Job_QueueInsertEmail;


class Job_QueuesEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function handle()
    {
        $customer = $this->customer;
        $this->Send_EMail($customer);
        sleep(1);
    }

    function Send_EMail()
    {
        $cus = $this->customer;
        // dd($cus);
        $res = $this->API_HTTP("https://app-a.nipamail.com/v2/transactional/multipost?accept_token=" . ENV('NIPAMAIL_TokenKey'), $cus);
        $json_res = json_decode($res);

        $this->InsertLoggedSendMail($json_res);
    }

    public function API_HTTP($url, $_data)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->post($url, $_data);
        $resData =  $response->body();

        return $resData;
    }

    function InsertLoggedSendMail($_res)
    {
        // dd($response);
        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now();

        $headId = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOGGED_EMAIL_HEADER')->insertGetId([
            // 'id' => null,
            'bulkId' => $_res->bulkId,
            'code' => $_res->code,
            'message' => $_res->message,
            'send_date' => $dateNow,
            'checked' => 0,
        ]);

        foreach ($_res->data as $val) {
            Job_QueueInsertEmail::dispatch($headId, $val)->onQueue('site_email_insert');
        }
    }
}
