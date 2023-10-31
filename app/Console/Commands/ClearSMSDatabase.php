<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearSMSDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:ClearSMSDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old data 3 months in database every daily using cron job.';

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

        $Months = Carbon::now()->subMonths(3);

        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            ->whereDate('DATE', '<', $Months)
            ->delete();     

    }
}
