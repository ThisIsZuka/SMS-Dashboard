<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Session;


class PageCustomer_Controller extends BaseController
{
    public function Customer_Information(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();
            // $Q_id = $data['Qid'];


            // Get Product
            $product_KYC = DB::table('dbo.PRODUCT')
                ->select('CATEGORY_NAME', 'MT_BRAND.BRAND_NAME', 'SERIES_NAME')
                ->leftJoin('MT_CATEGORY', 'PRODUCT_CATEGORY', '=', 'MT_CATEGORY.CATEGORY_ID')
                ->leftJoin('MT_BRAND', 'PRODUCT_BAND', '=', 'MT_BRAND.BRAND_ID')
                ->leftJoin('MT_SERIES', 'PRODUCT_SERIES', '=', 'MT_SERIES.SERIES_ID')
                ->where('APP_ID', $data['APP_ID'])
                ->get();

            $return_data->product = $product_KYC;


            // Get Credit
            $Customer_card = DB::table('dbo.CUSTOMER_CARD')
                ->select('CUSTOMER_CARD.INSTALL_NUM',  'CUSTOMER_CARD.DUEDATE',  'CUSTOMER_CARD.INSTALL_AMT', 'CUSTOMER_CARD.INVOICE_NUMBER', 'CUSTOMER_CARD.RECEIPT_NUMBER', 'INVOICE.INVOICE_ID', 'REPAYMENT.REPAY_ID', 'TAX_INVOICE.TAX_INVOICE_ID', 'TAX_INVOICE.TAX_NUMBER', 'INVOICE.SUM_AMT', 'CUSTOMER_CARD.CONTRACT_NUMBER')
                ->leftJoin('INVOICE', 'CUSTOMER_CARD.INVOICE_NUMBER', '=', 'INVOICE.INVOICE_NUMBER')
                ->leftJoin('REPAYMENT', 'CUSTOMER_CARD.RECEIPT_NUMBER', '=', 'REPAYMENT.RECEIPT_NUMBER')
                ->leftJoin('TAX_INVOICE', 'REPAYMENT.TAX_NUMBER', '=', 'TAX_INVOICE.TAX_NUMBER')
                ->where('CUSTOMER_CARD.APPLICATION_NUMBER', $data['APP_ID'])
                ->orderBy('CUSTOMER_CARD.INSTALL_NUM', 'ASC')
                ->get();
            $return_data->Customer_card = $Customer_card;


            // GET QRCode
            $REF_NO = $Customer_card[0]->CONTRACT_NUMBER;
            $QR_Code = DB::table('dbo.TTP_INV_BARCODE')
                ->select('TTP_INV_BARCODE.INV_NO',  'TTP_INV_BARCODE.DUE_DATE',  'TTP_INV_BARCODE.REF1_NO', 'TTP_INV_BARCODE.INV_AMT', 'TTP_INV_BARCODE.QRCODE_FILE')
                ->leftJoin('TTP_APPL_TRANS', 'TTP_INV_BARCODE.REF1_NO', '=', 'TTP_APPL_TRANS.PAYMENT_REF1')
                ->where('TTP_INV_BARCODE.REF1_NO', $REF_NO)
                ->where('TTP_APPL_TRANS.PAYMENT_STATUS', '1')
                ->orderBy('TTP_INV_BARCODE.DUE_DATE', 'DESC')
                ->get();
            $num_QR_Code = count($QR_Code);
            if ($num_QR_Code != 0) {
                $return_data->QR_Code = $QR_Code[0];
            }


            // PERSON Address
            $PERSON = DB::table('dbo.PERSON')
                ->select('PERSON_ID')
                ->where('APP_ID', $data['APP_ID'])
                ->get();

            // GET Address
            $Address = DB::table('dbo.ADDRESS')
                ->select('ADDRESS.A3_NO', 'ADDRESS.A3_MOI', 'ADDRESS.A3_SOI', 'ADDRESS.A3_VILLAGE', 'ADDRESS.A3_BUILDING', 'ADDRESS.A3_SOI', 'ADDRESS.A3_ROAD', 'MT_SUB_DISTRICT.SUB_DISTRICT_NAME', 'MT_DISTRICT.DISTRICT_NAME', 'MT_PROVINCE.PROVINCE_NAME', 'MT_POST_CODE.POST_CODE_ID')
                ->leftJoin('MT_SUB_DISTRICT', 'ADDRESS.A3_SUBDISTRICT', '=', 'MT_SUB_DISTRICT.SUB_DISTRICT_ID')
                ->leftJoin('MT_DISTRICT', 'ADDRESS.A3_DISTRICT', '=', 'MT_DISTRICT.DISTRICT_ID')
                ->leftJoin('MT_PROVINCE', 'ADDRESS.A3_PROVINCE', '=', 'MT_PROVINCE.PROVINCE_ID')
                ->leftJoin('MT_POST_CODE', 'ADDRESS.A3_SUBDISTRICT', '=', 'MT_POST_CODE.SUB_DISTRICT_ID')
                ->where('PERSON_ID', $PERSON[0]->PERSON_ID)
                ->get();
            $return_data->Address = $Address;


            // 
            $PDF_INSURANCE = DB::table('dbo.PDF_FORM')
                ->select('PDF_ID', 'APP_ID')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'INSURANCE')
                ->get();
            $num_PDF_INSURANCE = count($PDF_INSURANCE);
            if ($num_PDF_INSURANCE != 0) {
                $return_data->PDF_INSURANCE = $PDF_INSURANCE;
            }



            // Get Date Now
            $today = date('d/m/Y');
            $return_data->today = $today;


            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }

    public function PDF_INVOICE(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('INVOICE_ID', $data['PDF_ID'])
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CUSTOMER_CARD = DB::table('dbo.CUSTOMER_CARD')
                    ->select('ID', 'INVOICE_NUMBER')
                    ->where('APPLICATION_NUMBER', $data['APP_ID'])
                    ->where('INVOICE_NUMBER', $data['Number'])
                    ->get();

                $INVOICE = DB::table('dbo.INVOICE')
                    ->select('INVOICE_ID', 'INVOICE_NUMBER')
                    ->where('INVOICE_NUMBER', $data['Number'])
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0  && count($CUSTOMER_CARD) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                    $return_data->URL_APP['ID'] = $CUSTOMER_CARD[0]->ID;
                    $return_data->URL_APP['INVOICE_ID'] = $INVOICE[0]->INVOICE_ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }

    public function PDF_REPAY(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('REPAY_ID', $data['PDF_ID'])
                ->where('PDF_TYPE', 'RPINSTALLMENT')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CUSTOMER_CARD = DB::table('dbo.CUSTOMER_CARD')
                    ->select('ID', 'INVOICE_NUMBER')
                    ->where('APPLICATION_NUMBER', $data['APP_ID'])
                    ->where('RECEIPT_NUMBER', $data['Number'])
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0  && count($CUSTOMER_CARD) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                    $return_data->URL_APP['REPAY_ID'] = $data['PDF_ID'];
                    $return_data->URL_APP['ID'] = $CUSTOMER_CARD[0]->ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }

    public function PDF_TAX_INVOICE(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('TAX_INVOICE_ID', $data['PDF_ID'])
                ->where('PDF_TYPE', 'TAXINVOICE')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CUSTOMER_CARD = DB::table('dbo.CUSTOMER_CARD')
                    ->select('ID', 'INVOICE_NUMBER')
                    ->where('APPLICATION_NUMBER', $data['APP_ID'])
                    ->where('RECEIPT_NUMBER', $data['Number'])
                    ->get();

                $REPAYMENT = DB::table('dbo.REPAYMENT')
                    ->select('REPAY_ID')
                    ->where('RECEIPT_NUMBER', $data['Number'])
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0  && count($CUSTOMER_CARD) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                    $return_data->URL_APP['REPAY_ID'] = $REPAYMENT[0]->REPAY_ID;
                    $return_data->URL_APP['ID'] = $CUSTOMER_CARD[0]->ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }


    public function PDF_CONTRACT(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'CONTRACT')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }

    public function PDF_TBDOWN(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'INTEREST TABLE')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }


    public function PDF_RPDOWN(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'RPDOWN')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $REPAYMENT = DB::table('dbo.REPAYMENT')
                    ->select('REPAY_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->where('REPAY_TYPE', '1')
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0 && count($REPAYMENT) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                    $return_data->URL_APP['REPAY_ID'] = $REPAYMENT[0]->REPAY_ID;
                }
            }


            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }


    public function PDF_TPDOWN(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'TPDOWN')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            if (count($return_data->PDF_Base64) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $CONTRACT = DB::table('dbo.CONTRACT')
                    ->select('CONTRACT_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                $REPAYMENT = DB::table('dbo.REPAYMENT')
                    ->select('REPAY_ID', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->where('REPAY_TYPE', '1')
                    ->get();

                if (count($APPLICATION) != 0 && count($CONTRACT) != 0 && count($REPAYMENT) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                    $return_data->URL_APP['CONTRACT_ID'] = $CONTRACT[0]->CONTRACT_ID;
                    $return_data->URL_APP['REPAY_ID'] = $REPAYMENT[0]->REPAY_ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }

    public function PDF_INSURANCE(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                ->where('PDF_TYPE', 'INSURANCE')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_Base64 = $PDF;

            return $return_data;
        } catch (Exception $e) {
            // return response()->json(array('message' => $e->getMessage()));
            return response()->json(array('message' => 'ERROR'));
        }
    }


    public function QR_Code(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            $QR = DB::table('dbo.TTP_INV_BARCODE')
                ->select('*')
                ->where('INV_NO', $data['Ref_NO'])
                ->orderBy('DUE_DATE', 'DESC')
                ->get();
            $return_data->QR_Code = $QR[0];

            return $return_data;
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()));
            // return response()->json(array('message' => 'ERROR'));
        }
    }


    public function TEST_API()
    {
        try {

            // $url = 'www.your-domain.com/api.php?to=' . $mobile . '&text=' . $message;
            $url = 'https://ufund.comseven.com/Runtime/Runtime/View/TestCall.ItemView/';

            //Your username.
            $username = 'k2admin';

            //Your password.
            $password = 'Com@7ktwo#';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $err = curl_error($ch);  //if you need
            curl_close($ch);

            //Check for errors.
            if (curl_errno($ch)) {
                //If an error occured, throw an Exception.
                throw new Exception(curl_error($ch));
            }
            var_dump($response);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()));
        }
    }
}
