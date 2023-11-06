<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Exception;
use App\Models\TTP_INV_BARCODE;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Job_QueuePerformanceServerUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $list;

    public $prdURL;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($list)
    {
        $this->list = $list;

        $this->prdURL = 'https://ufund.comseven.com/CM7T2P/barcode/APIGen';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $start_time = time();
        foreach ($this->list as $value) {

            $TTP_INV_BARCODE = TTP_INV_BARCODE::where('SEQ_ID', $value)->first();

            $gs1Data = "|010555801180600\r{$TTP_INV_BARCODE->REF1_NO}\r{$TTP_INV_BARCODE->REF2_NO}\r{$TTP_INV_BARCODE->INV_AMT}";

            $QR_Code = $this->GenQR_Code($TTP_INV_BARCODE, $gs1Data);
            $Barcode = $this->GenBarcode($TTP_INV_BARCODE, $gs1Data);

            $TTP_INV_BARCODE->BARCODE_FILE = $Barcode;
            $TTP_INV_BARCODE->QRCODE_FILE = $QR_Code;
            $TTP_INV_BARCODE->CRT_FLG = 'Y';
            $TTP_INV_BARCODE->update();
        }
        $end_time = time();
        Log::channel('logPerformance')->info("จำนวนรอบ:". count($this->list) ."|| เวลา : ". ($end_time-$start_time)). "s";
    }

    function GenQR_Code($val, $gs1Data)
    {
        try {
            $name_QR = "{$val->SEQ_ID}_QrCode_{$val->INV_NO}.png";

            // Generate the QR-Code
            $qrCode = QrCode::encoding('UTF-8')
                ->format('png')
                ->size(120)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($gs1Data);

            // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/QR_Code{$val->SEQ_ID}_{$val->INV_NO}.png", $qrCode);
            Storage::disk('public')->put("/INV_Gen/{$name_QR}", $qrCode);

            return "<img src={$this->prdURL}/INV_Gen/{$name_QR}></img>";
        } catch (Exception $e) {
            Log::error('Caught exception GenQR_Code: ' . $e->getMessage());
            throw $e;
        }
    }

    function GenBarcode($val, $gs1Data)
    {

        try {
            $name_Barcode = "{$val->SEQ_ID}_Barcode_{$val->INV_NO}.png";

            // Generate the barcode
            $barcode =  DNS1D::getBarcodePNG($gs1Data, 'C128', 1, 50, array(0, 0, 0), false);

            // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/Barcode{$val->SEQ_ID}_{$val->INV_NO}.png", base64_decode($barcode));
            Storage::disk('public')->put("INV_Gen/{$name_Barcode}", base64_decode($barcode));

            return "<img src={$this->prdURL}/INV_Gen/{$name_Barcode}></img>";
        } catch (Exception $e) {
            Log::error('Caught exception GenBarcode: ' . $e->getMessage());
            throw $e;
        }
    }
}
