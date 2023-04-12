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


class Job_QueueInsertEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $HeadId;
    public $listMail;

    public function __construct($HeadId, $listMail)
    {
        $this->HeadId = $HeadId;
        $this->listMail = $listMail;
    }

    public function handle()
    {
        $HeadId = $this->HeadId;
        $listMail = $this->listMail;
        $this->InsertLoggedSendMail($HeadId, $listMail);
        sleep(1);
    }

    function InsertLoggedSendMail($id, $list)
    {
        date_default_timezone_set('Asia/bangkok');
        $dateNow = Carbon::now()->timestamp;

        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOGGED_EMAIL_LISTS')->insert([
                "header_id" => $id,
                "message_bulkId" => $list->bulkId,
                "message_transId" => $list->tranId,
                "form" => $list->from,
                "to" => $list->to,
                // "user_open" => null,
                // "user_click" => null,
                // "timestamp" => null,
                // "status" => null,
                // "errors" => null,
                // "create_date" => $dateNow,
                // "update_date" => null,
            ]);
    }
}
