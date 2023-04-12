<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Milon\Barcode\BarcodeServiceProvider;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

use Illuminate\Support\Facades\Storage;

class Job_QueuesConvertQR_Barcode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $SEQ_ID;
    public $Date;

    public $prdURL;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($SEQ_ID, $Date)
    {
        $this->SEQ_ID = $SEQ_ID;
        $this->Date = $Date;

        $this->prdURL = 'https://ufund.comseven.com/CM7T2P/barcode/APIGen';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $SEQ_ID = $this->SEQ_ID;
        $this->Convert_ImgInv($SEQ_ID);
        sleep(1);
    }

    function Convert_ImgInv($SEQ_ID)
    {
        try {

            $val = DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
                ->select('*')
                ->where('SEQ_ID', $SEQ_ID)
                ->first();

            $gs1Data = "|010555801180600\r{$val->REF1_NO}\r{$val->REF2_NO}\r{$val->INV_AMT}";

            $QR_Code = $this->GenQR_Code($val, $gs1Data);
            $Barcode = $this->GenBarcode($val, $gs1Data);

            date_default_timezone_set('Asia/bangkok');
            $dateNow = Carbon::now();

            DB::connection('sqlsrv_HPCOM7')->table('dbo.TTP_INV_BARCODE')
                ->where('SEQ_ID',  $val->SEQ_ID)
                ->update([
                    'BARCODE_FILE' => $Barcode,
                    'QRCODE_FILE' => $QR_Code,
                    'CRT_FLG' => 'Y',
                    'UPDATE_DATE' => $dateNow,
                    'UPDATE_BY' => 'SYSTEM',
                ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function GenQR_Code($val, $gs1Data)
    {
        try {
            $name_QR = "{$val->SEQ_ID}_QR_Code_{$val->INV_NO}.png";

            // Generate the QR-Code
            $qrCode = QrCode::encoding('UTF-8')
                ->format('png')
                ->size(120)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($gs1Data);

            // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/QR_Code{$val->SEQ_ID}_{$val->INV_NO}.png", $qrCode);
            Storage::disk('sftp_K2_PRD')->put("/INV_Gen_{$this->Date}/{$name_QR}", $qrCode);

            return "<img src={$this->prdURL}/INV_Gen_{$this->Date}/{$name_QR}></img>";
        } catch (Exception $e) {
            return '';
        }
    }

    function GenBarcode($val, $gs1Data)
    {

        try {
            $name_Barcode = "{$val->SEQ_ID}_Barcode_{$val->INV_NO}.png";

            // Generate the barcode
            $barcode =  DNS1D::getBarcodePNG($gs1Data, 'C128', 1, 50, array(0, 0, 0), false);

            // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/Barcode{$val->SEQ_ID}_{$val->INV_NO}.png", base64_decode($barcode));
            Storage::disk('sftp_K2_PRD')->put("INV_Gen_{$this->Date}/{$name_Barcode}", base64_decode($barcode));

            return "<img src={$this->prdURL}/INV_Gen_{$this->Date}/{$name_Barcode}></img>";
        } catch (Exception $e) {
            return '';
        }
    }
}
