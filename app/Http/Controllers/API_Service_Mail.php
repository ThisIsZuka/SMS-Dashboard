<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;
use stdClass;

use App\Jobs\Job_QueuesEmail;

class API_Service_Mail extends BaseController
{

    public function Job_SendMail($req){

        $list = (array_chunk($req,5000));
        foreach($list as $key => $val){
            $message = $this->setupData($val);
            Job_QueuesEmail::dispatch($message)->onQueue('site_main_email');
        }
        // dd(count($list));
    }

    public function API_HTTP($url, $_data)
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
        ])->post($url, $_data);
        $resData =  $response->body();

        return $resData;
    }

    public function PostRequest_Mail_V2()
    {

        $list_sendSMS = DB::connection('sqlsrv_HPCOM7')->select(DB::connection('sqlsrv_HPCOM7')->raw("exec SP_Get_Invoice_SMS  @DateInput = '2023-01-01' "));

        $message = $this->setupData($list_sendSMS);

        $res = $this->API_HTTP("https://app-a.nipamail.com/v2/transactional/multipost?accept_token=" . ENV('NIPAMAIL_TokenKey'), $message);

        $json_res = json_decode($res);
        dd($json_res);
    }


    function Get_Name($name)
    {
        $clean_name = trim(preg_replace('/^(นางสาว|นาย|นาง)/u', '', $name));

        $fname = explode(" ", $clean_name)[0];
        $lname = explode(" ",  $clean_name)[1];

        return [$fname, $lname];
    }


    function setupData($list)
    {
        $arr_msg = [];

        foreach ($list as $key => $val) {
            list($fname, $lname) = $this->Get_Name($val->CUSTOMER_NAME);

            $obj_cus = new stdClass();
            $obj_cus->fname = $fname;
            $obj_cus->lname = $lname;
            $obj_cus->URL = $val->INV_URL;

            $msg = new stdClass();
            $msg->from_name = "UFUND";
            $msg->from_email = "info@Thunderfinfin.com";
            $msg->to = $val->EMAIL;
            // $msg->to = "kittisak.u@comseven.com";
            $msg->parameters = $obj_cus;

            array_push($arr_msg, $msg);
        }

        $data_array = array(
            'subject' => 'ใบแจ้งชำระค่างวดเช่าซื้อ UFUND',
            'template_id' => '4231',
            'message' => $arr_msg,
        );
        return $data_array;
    }

}
