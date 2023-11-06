<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\TTP_INV_BARCODE;
use App\Jobs\Job_QueuePerformanceServerUpdate;

class Job_QueuePerformanceServerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $countnum;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($countnum)
    {
        $this->countnum = $countnum;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $seq_id = array();
        for ($i = 0; $i < $this->countnum; $i++) {
            // echo $i;

            $randomNumber = mt_rand(1000, 9999);

            $randomREF1_NO = mt_rand(2100001, 2199999);
            
            $model = new TTP_INV_BARCODE;
            $model->INV_NO = $i;
            $model->INV_AMT = $randomNumber;
            $model->REF1_NO = $randomREF1_NO;
            $model->REF2_NO = 1;
            $model->CRT_FLG = 'N';
            $model->save();

            $seq_id [] = $model->SEQ_ID;
        }

        Job_QueuePerformanceServerUpdate::dispatch($seq_id)->onQueue('PerformanceServer');
    }
}
