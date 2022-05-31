<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class API_Service_Mail extends BaseController
{
    public function PostRequest_Mail()
    {
        try {

            // $_data = array(
            //     // 'accept_token' => 'NPAPP-W0GqDACGulzhYmaWknroIftEd1k1K2xU8EXHP3zQo4OrDxYZvpMPO9eI5ybZsLyJvTgUchnd7S25NwKQJm8RNqAubTiMp370210me32tVFsghvyJ07MY4nWoSXHDcPEwZ6ArNTj5pRalifdIbOqCk870',
            //     'from_email' => 'ufund@comseven.com',
            //     'from_name' => 'UFUND',
            //     'to' => 'kittisak.u@comseven.com',
            //     'subject' => 'Test Ufund',
            //     'message' => '$htmlTxt',
            //     // 'body'=> '[fname] [lname]',
            //     // 'template_id' => 4231,
            // );
            // // echo '<pre>' . var_export($_data, true) . '</pre>';
            // $payload = '';
            // $eol = "\r\n";
            // $boundary = 'WebKitFormBoundary7MA4YWxkTrZu0gW';
            // foreach ($_data as $name => $content) {
            //     $payload .= "------" . $boundary . $eol . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol . $content . $eol;
            // }
            // $payload = (array("message"=> $_data));
            // dd($payload);

            $obj = new \StdClass();
            $obj->fname = 'Test';
            $obj->lname = 'Test';
            $obj->URL = 'https://www.ufundportal.com';


            $_data2 = new \StdClass();
            $_data2->from_name = 'UFUND';
            $_data2->from_email = 'info@thunderfinfin.com';
            $_data2->to = 'c15suchart.si@gmail.com';
            $_data2->subject = 'Test UFUND';
            // $_data2->message = '$htmlTxt <a href="www.ufundportal.com"> คลิ๊ก </a>';
            $_data2->parameters = $obj;
            $_data2->template_id = 4231;
            $_data2->disable_unsubscribe = 'true';
            // dd($_data2);
            // dd(json_encode($_data2));
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://app-x.nipamail.com/v1.0/transactional/post?accept_token=NPAPP-W0GqDACGulzhYmaWknroIftEd1k1K2xU8EXHP3zQo4OrDxYZvpMPO9eI5ybZsLyJvTgUchnd7S25NwKQJm8RNqAubTiMp370210me32tVFsghvyJ07MY4nWoSXHDcPEwZ6ArNTj5pRalifdIbOqCk870",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"from_name\"\r\n\r\nThaksinai Kondee\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"from_email\"\r\n\r\nkittisak.u@comseven.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"to\"\r\n\r\nkittisak.u@comseven.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"subject\"\r\n\r\nร่วมฉลอง ครบรอบการก่อตั้ง บริษัท Nipa technology วันนี้ เวลา 18:00 น.\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"message\"\r\n\r\ncontent1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"reply_email\"\r\n\r\nse55660159@gmail.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"reply_name\"\r\n\r\nThakweb.com\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
                CURLOPT_POSTFIELDS => json_encode($_data2),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                ),
                // CURLOPT_HTTPHEADER => array(
                //     "cache-control: no-cache",
                //     "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
                // ),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            // $this->response['response'] = json_decode($response);

            if ($err) {
                // echo "cURL Error #:" . $err;
                dump("cURL Error #:" . $err);
            } else {
                // echo $response;
                $res = json_decode($response);
                dd($res);
            }
        } catch (Exception $e) {
            $return_data = new \stdClass();

            $return_data->code = '000000';
            $return_data->message =  $e->getMessage();

            return $return_data;
        }
    }
}
