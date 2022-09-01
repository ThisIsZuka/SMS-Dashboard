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
use Illuminate\Auth\Events\Failed;
use Throwable;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $data;

    public $tries = 2;
    public $backoff = 1;

    public static $newData;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $data = $this->data;
        // dd(ENV('MAILBIT_USER'));
        dd('http://sms.mailbit.co.th/vendorsms/checkdelivery.aspx?user' . ENV('MAILBIT_USER') . '=&password=ufund@2022&messageid=');
        // date_default_timezone_set('Asia/bangkok');
        // $dateNow = date('Y-m-d');

        // $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //     ->selectRaw('ISNULL(MAX(RUNNING_NO) + 99 ,1) as new_id')
        //     ->where('date', $dateNow)
        //     ->value('new_id');

        // self::$newData = 'test';

        // DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
        //     'DATE' => $dateNow,
        //     // 'RUNNING_NO' => DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //     //     ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
        //     //     ->where('date', $dateNow)
        //     //     ->value('new_id'),
        //     'RUNNING_NO' => 'sss',
        //     'QUOTATION_ID' => $data,
        // ]);

        sleep(1);

        // try {

        //     date_default_timezone_set('Asia/bangkok');
        //     $dateNow = date('Y-m-d');

        //     $try = 'testt';

        //     $new_id = DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //         ->selectRaw('ISNULL(MAX(RUNNING_NO) + 99 ,1) as new_id')
        //         ->where('date', $dateNow)
        //         ->value('new_id');

        //     DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
        //         'DATE' => $dateNow,
        //         // 'RUNNING_NO' => DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
        //         //     ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
        //         //     ->where('date', $dateNow)
        //         //     ->value('new_id'),
        //         'RUNNING_NO' => 'sss',
        //         'QUOTATION_ID' => $data,
        //     ]);

        //     sleep(1);

        // } catch (Exception $e) {
        //     // echo $data;
        //     if ($this->attempts() > 2) {
        //         // hard fail after 3 attempts
        //         // throw $exception;
        //         $this->failed($e);
        //     }
        //     echo $this->attempts();
        //     return -2;
        //     // $this->release(180);
        //     // return;
        //     // $this->failed($e);
        // }
    }

    public function failed($e)
    {
        // dd($e);
        // $new = $this->newData;
        dd(self::$newData);
        // echo $e->getMessage();
        // $exception->getMessage();
        // echo $exception->getMessage();
        date_default_timezone_set('Asia/bangkok');
        $dateNow = date('Y-m-d');

        DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')->insert([
            'DATE' => $dateNow,
            // 'RUNNING_NO' => DB::connection('sqlsrv_HPCOM7')->table('dbo.LOG_SEND_SMS')
            //     ->selectRaw('ISNULL(MAX(RUNNING_NO) + 1 ,1) as new_id')
            //     ->where('date', $dateNow)
            //     ->value('new_id'),
            'RUNNING_NO' => '1',
            'QUOTATION_ID' => '$new',
        ]);
    }
}
