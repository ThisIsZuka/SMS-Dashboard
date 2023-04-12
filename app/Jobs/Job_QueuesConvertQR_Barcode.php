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

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Milon\Barcode\BarcodeServiceProvider;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

use Illuminate\Support\Facades\Storage;

class Job_QueuesConvertQR_Barcode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $customer;
    public $Date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer, $Date)
    {
        $this->customer = $customer;
        $this->Date = $Date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cus = $this->customer;
        $this->Convert_ImgInv($cus);
        sleep(1);
    }

    function Convert_ImgInv($val)
    {
        try {
            $gs1Data = "|010555801180600\r{$val->REF1_NO}\r{$val->REF2_NO}\r{$val->INV_AMT}";

            $this->GenQR_Code($val, $gs1Data);

            $this->GenBarcode($val, $gs1Data);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function GenQR_Code($val, $gs1Data)
    {
        $name_QR = "QR_Code{$val->SEQ_ID}_{$val->INV_NO}.png";

        // Generate the QR-Code
        $qrCode = QrCode::encoding('UTF-8')
            ->format('png')
            ->size(120)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($gs1Data);

        // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/QR_Code{$val->SEQ_ID}_{$val->INV_NO}.png", $qrCode);
        Storage::disk('sftp_K2_PRD')->put("/INV_Gen_{$this->Date}/{$name_QR}", $qrCode);
    }

    function GenBarcode($val, $gs1Data)
    {

        $name_Barcode = "Barcode{$val->SEQ_ID}_{$val->INV_NO}.png";

        // Generate the barcode
        $barcode =  DNS1D::getBarcodePNG($gs1Data, 'C128', 1, 50, array(0, 0, 0), false);

        // Storage::disk('public_uploads')->put("INV_Gen_{$this->DateNow}/Barcode{$val->SEQ_ID}_{$val->INV_NO}.png", base64_decode($barcode));
        Storage::disk('sftp_K2_PRD')->put("INV_Gen_{$this->Date}/{$name_Barcode}", base64_decode($barcode));
    }
}
