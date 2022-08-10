<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

use App\Jobs\Job_QueueCheckSMSDeliver;

class Check_SMS_Deliver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:CheckSMSDeliver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Status Delivery SMS every midnight using cron job.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $LOG_SEND_SMS = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->select('SMS_ID', 'SMS_RESPONSE_MSG_ID', 'SMS_Status_Delivery')
            ->where('SMS_RESPONSE_CODE', '000')
            ->where(function ($query) {
                $query->where('SMS_Status_Delivery', '');
                $query->orWhereNull('SMS_Status_Delivery');
            })
            ->get();

        if (count($LOG_SEND_SMS) == 0) return 0;


        for ($i = 0; $i < count($LOG_SEND_SMS); $i++) {

            Job_QueueCheckSMSDeliver::dispatch($LOG_SEND_SMS[$i])->onQueue('site_main');
        }
    }
}
