<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Exception;

class WorkflowK2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:WorkflowK2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WorkflowK2 cron job.';

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
        try{
            $data_arry = array(
                'folio' => Carbon::now()->format('Y-m-d H:i:s'),
            );
    
            $param = '101';
    
            $this->PostRequest_SMS("https://ufund.comseven.com/Api/Workflow/Preview/workflows/$param", $data_arry);
    
            return 0;
        }catch(Exception $e){
            return $e->getMessage();
        }

    }

    public function PostRequest_SMS($url, $_data)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'Authorization' => 'Basic Y29tc2V2ZW4yMDE5XGsyYWRtaW46Q29tQDdrdHdvYWRtaW4jIyM=',
        ])->post($url, $_data);
        $resData =  $response->body();

        return $resData;
    }

}
