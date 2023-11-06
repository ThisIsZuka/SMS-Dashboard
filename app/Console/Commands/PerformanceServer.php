<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Job_QueuePerformanceServerInsert;

class PerformanceServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:TestPerformanceServer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the performance of the server';

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
        $randomNumber = mt_rand(1, 100);

        Job_QueuePerformanceServerInsert::dispatch($randomNumber)->onQueue('PerformanceServer');
    }
}
