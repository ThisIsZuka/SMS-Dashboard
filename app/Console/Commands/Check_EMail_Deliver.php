<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Check_EMail_Deliver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:CheckEMailDeliver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Status Delivery E-Mail every midnight using cron job.';

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
        return 0;
    }

}
