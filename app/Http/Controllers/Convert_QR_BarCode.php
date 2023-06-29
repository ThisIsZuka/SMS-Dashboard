<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

use Illuminate\Support\Facades\Storage;

use App\Jobs\Job_QueuesConvertQR_Barcode;


class Convert_QR_BarCode extends BaseController
{

    public $DateNow;

    public function __construct()
    {
        $this->DateNow = Carbon::now()->format("d_m_Y");
    }

    public function MainReq()
    {
    }

    public function generateBarcode(Request $request)
    {
        try {
            // dd($this->DateNow);

            $data = $request->all();
            $DUE_DATE = $data['DUE_DATE'];
            $INV_DATE = $data['INV_DATE'];

            $TTP_INV_BARCODE = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
                ->select('SEQ_ID')
                ->where('INV_DATE', $INV_DATE)
                ->where('CRT_FLG', '!=' , 'Y')
                // ->limit(1)
                ->get();

            foreach ($TTP_INV_BARCODE as $key => $val) {
                Job_QueuesConvertQR_Barcode::dispatch($val->SEQ_ID, $this->DateNow)->onQueue('site_main');
            }

            return 'success';
        } catch (Exception $e) {
            Log::error('Caught exception: ' . $e->getMessage());
        }
    }
}
