<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Check_SMS_Deliver::class,
        Commands\CleanOldLogEntries::class,
        Commands\ClearSMSDatabase::class,
        Commands\PerformanceServer::class,
        Commands\WorkflowK2::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('cron:CheckSMSDeliver')->daily();
        $schedule->command('log:clean')->daily();
        $schedule->command('cron:ClearSMSDatabase')->daily();
        $schedule->command('cron:WorkflowK2')->dailyAt('23:30');
        // $schedule->command('cron:CheckSMSDeliver')->everyMinute();
        // $schedule->command('cron:TestPerformanceServer')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
