<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;

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

    public function generateBarcode()
    {
        try {
            // dd($this->DateNow);
            $TTP_INV_BARCODE = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
                ->select('*')
                ->where('INV_DATE', '2023-01-01')
                ->limit(2)
                ->get();
            // dd($TTP_INV_BARCODE);
            foreach ($TTP_INV_BARCODE as $key => $val) {
                Job_QueuesConvertQR_Barcode::dispatch($val, $this->DateNow)->onQueue('site_main');
            }

            return 'success';
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
