<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Mockery\Undefined;
use Session;


class PageMilestore_Controller extends BaseController
{
    public function Check_state(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();
            // $Q_id = $data['Qid'];
            // $APP_ID = $data['APP_ID'];

            if (isset($data['Qid'])) {
                // wait approve
                $QT_status27 = DB::table('dbo.QUOTATION')
                    ->select('QUOTATION_ID', 'CUSTOMER_NAME', 'STATUS_ID')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->where('STATUS_ID', '27')
                    ->get();

                $check = 0;
                $check_QT_status27 = count($QT_status27);
                if ($check_QT_status27 == 0) {
                    $Stepapprove = DB::table('dbo.APPLICATION')
                        ->select('CHECKER_RESULT', 'CUSTOMER_NAME', 'APP_ID')
                        ->where('QUOTATION_ID', $data['Qid'])
                        ->orderBy('APP_ID', 'DESC')
                        ->get();
                    $check = count($Stepapprove);
                }


                // Get Product
                $product_KYC = DB::table('dbo.PRODUCT')
                    ->select('CATEGORY_NAME', 'MT_BRAND.BRAND_NAME', 'SERIES_NAME')
                    ->leftJoin('MT_CATEGORY', 'PRODUCT_CATEGORY', '=', 'MT_CATEGORY.CATEGORY_ID')
                    ->leftJoin('MT_BRAND', 'PRODUCT_BAND', '=', 'MT_BRAND.BRAND_ID')
                    ->leftJoin('MT_SERIES', 'PRODUCT_SERIES', '=', 'MT_SERIES.SERIES_ID')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->orderBy('APP_ID', 'DESC')
                    ->get();

                $product_Regis = DB::table('dbo.QUOTATION')
                    ->select('CATEGORY_NAME', 'MT_BRAND.BRAND_NAME', 'SERIES_NAME')
                    ->leftJoin('MT_CATEGORY', 'PRODUCT_CATEGORY', '=', 'MT_CATEGORY.CATEGORY_ID')
                    ->leftJoin('MT_BRAND', 'PRODUCT_BAND', '=', 'MT_BRAND.BRAND_ID')
                    ->leftJoin('MT_SERIES', 'PRODUCT_SERIES', '=', 'MT_SERIES.SERIES_ID')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->get();


                // Get APPROVE_CODE
                $APPROVE_CODE = DB::table('dbo.QUOTATION')
                    ->select('APPROVE_CODE')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->get();
                $return_data->APPROVE_CODE = $APPROVE_CODE[0]->APPROVE_CODE;

                $Money_Down = DB::table('dbo.QUOTATION')
                    ->select('DOWN_SUM_AMT')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->get();
                $return_data->Money_Down = $Money_Down[0]->DOWN_SUM_AMT;

                $Company = DB::table('dbo.QUOTATION')
                    ->select('BRANCH_SHORT_NAME')
                    ->leftJoin('SETUP_COMPANY_BRANCH', 'BRANCH_ID', '=', 'SETUP_COMPANY_BRANCH.COMP_BRANCH_ID')
                    ->where('QUOTATION_ID', $data['Qid'])
                    ->get();
                $return_data->Company = $Company[0]->BRANCH_SHORT_NAME;


                // GET_QR_Code
                if ($check != 0) {
                    $SMS_REPAY_Down = DB::table('dbo.TTP_SMS_RESULT')
                        ->select('SEQ_ID', 'REF_NO1', 'REF_NO2', 'PAY_AMT', 'MOBILE_NO', 'SEND_STATUS', 'SEND_RESULT', 'SEND_MSG', 'APP_ID', 'APPLICATION_NUMBER')
                        ->leftJoin('APPLICATION', 'APPLICATION.APPLICATION_NUMBER', '=', 'TTP_SMS_RESULT.REF_NO1')
                        ->where('APPLICATION.APP_ID', $Stepapprove[0]->APP_ID)
                        ->get();
                    // dd($SMS_REPAY_Down);
                    $check_SMS_REPAY_Down = count($SMS_REPAY_Down);
                    if ($check_SMS_REPAY_Down != 0) {
                        $Status_Pay_Down = DB::table('dbo.REPAYMENT')
                            ->select('*')
                            ->where('APPLICATION_NUMBER', $SMS_REPAY_Down[0]->APPLICATION_NUMBER)
                            ->where('REPAY_TYPE', '1')
                            ->where('RECEIPT_NUMBER', '!=', 'null')
                            ->where('TAX_NUMBER', '!=', 'null')
                            // ->where('PAY_NAME', '!=', 'null')
                            ->get();

                        $check_Status_Pay_Down = count($Status_Pay_Down);
                        if ($check_Status_Pay_Down == 0) {
                            $Status_Pay_DownStep2 = DB::table('dbo.REPAYMENT')
                                ->select('*')
                                ->where('APPLICATION_NUMBER', $SMS_REPAY_Down[0]->APPLICATION_NUMBER)
                                ->where('REPAY_TYPE', '1')
                                ->where('PAY_NAME', '!=', 'null')
                                ->get();
                            // dd($Status_Pay_DownStep2);
                            $check_Status_Pay_DownStep2 = count($Status_Pay_DownStep2);
                            if ($check_Status_Pay_DownStep2 == 0) {
                                $return_data->QR_Down = $SMS_REPAY_Down[0];
                            }
                            // $return_data->QR_Down = $SMS_REPAY_Down[0];
                        }
                    }
                }


                $num_product_KYC = count($product_KYC);
                if ($num_product_KYC != 0) {
                    $return_data->product = $product_KYC;
                } else {
                    $return_data->product = $product_Regis;
                }

                // Get CONTRACT
                if ($check != 0) {
                    $CONTRACT = DB::table('dbo.CONTRACT')
                        ->select('APP_ID', 'CONTRACT_NUMBER')
                        ->where('APP_ID', $Stepapprove[0]->APP_ID)
                        ->get();
                    $check_CONTRACT = count($CONTRACT);
                    if ($check_CONTRACT != 0) {
                        $return_data->CONTRACT = $CONTRACT;
                    }
                }


                // Step 1
                if ($check == 0) {
                    $return_data->step = 'StepWaitKYC';
                    // return $return_data;
                }
                // Step 2
                else if ($Stepapprove[0]->CHECKER_RESULT == NULL) {
                    $return_data->APP_ID = $Stepapprove[0]->APP_ID;
                    $return_data->step = 'StepWaitApprove';
                } else {

                    $return_data->APP_ID = $Stepapprove[0]->APP_ID;

                    if ($Stepapprove[0]->CHECKER_RESULT == 'Approve') {

                        $StepDeliver = DB::table('dbo.CONTRACT')
                            ->select('APP_ID', 'STA_NAME')
                            ->leftJoin('MT_STATUS', 'STATUS_ID', '=', 'MT_STATUS.HP_STA_ID')
                            ->where('APP_ID', $Stepapprove[0]->APP_ID)
                            ->get();

                        $checkDeliver = count($StepDeliver);

                        if ($checkDeliver == 0) {
                            $return_data->step = 'StepApprove';
                        } else {
                            $return_data->step = 'StepDeliver';
                        }
                    } else if ($Stepapprove[0]->CHECKER_RESULT == 'Rework') {
                        $etc = DB::table('dbo.APPROVAL_HISTORY')
                            ->select('*')
                            ->where('APP_ID', $Stepapprove[0]->APP_ID)
                            ->where('STATUS_ID', '5')
                            ->get();

                        $return_data->etc = $etc;
                        $return_data->step = 'StepRework';
                    } else if ($Stepapprove[0]->CHECKER_RESULT == 'Reject') {
                        $return_data->step = 'StepReject';
                    }
                }

                return $return_data;
            } else {
                // wait approve
                $Stepapprove = DB::table('dbo.APPLICATION')
                    ->select('CHECKER_RESULT', 'CUSTOMER_NAME', 'APP_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->orderBy('APP_ID', 'DESC')
                    ->get();
                $check = count($Stepapprove);

                // Get Product
                $product_KYC = DB::table('dbo.PRODUCT')
                    ->select('CATEGORY_NAME', 'MT_BRAND.BRAND_NAME', 'SERIES_NAME')
                    ->leftJoin('MT_CATEGORY', 'PRODUCT_CATEGORY', '=', 'MT_CATEGORY.CATEGORY_ID')
                    ->leftJoin('MT_BRAND', 'PRODUCT_BAND', '=', 'MT_BRAND.BRAND_ID')
                    ->leftJoin('MT_SERIES', 'PRODUCT_SERIES', '=', 'MT_SERIES.SERIES_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->orderBy('APP_ID', 'DESC')
                    ->get();

                $return_data->product = $product_KYC;

                // Step 1
                if ($check == 0) {
                    $return_data->step = 'StepWaitKYC';
                    // return $return_data;
                }
                // Step 2
                else if ($Stepapprove[0]->CHECKER_RESULT == NULL) {
                    $return_data->APP_ID = $Stepapprove[0]->APP_ID;
                    $return_data->step = 'StepWaitApprove';
                } else {

                    $return_data->APP_ID = $Stepapprove[0]->APP_ID;

                    if ($Stepapprove[0]->CHECKER_RESULT == 'Approve') {

                        $StepDeliver = DB::table('dbo.CONTRACT')
                            ->select('APP_ID', 'STA_NAME')
                            ->leftJoin('MT_STATUS', 'STATUS_ID', '=', 'MT_STATUS.HP_STA_ID')
                            ->where('APP_ID', $Stepapprove[0]->APP_ID)
                            ->get();

                        $checkDeliver = count($StepDeliver);

                        if ($checkDeliver == 0) {
                            $return_data->step = 'StepApprove';
                        } else {
                            $return_data->step = 'StepDeliver';
                        }
                    } else if ($Stepapprove[0]->CHECKER_RESULT == 'Rework') {
                        $etc = DB::table('dbo.APPROVAL_HISTORY')
                            ->select('*')
                            ->where('APP_ID', $Stepapprove[0]->APP_ID)
                            ->where('STATUS_ID', '5')
                            ->get();

                        $return_data->etc = $etc;
                        $return_data->step = 'StepRework';
                    } else if ($Stepapprove[0]->CHECKER_RESULT == 'Reject') {
                        $return_data->step = 'StepReject';
                    }
                }
                return $return_data;
            }
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()));
        }
    }

    public function PDF_APP(Request $request)
    {
        try {
            $return_data = new \stdClass();

            $data = $request->all();

            // Get PDF
            $PDF = DB::table('dbo.PDF_FORM')
                ->select('PDF_NAME')
                ->where('APP_ID', $data['APP_ID'])
                // ->where('APP_ID', '99999999')
                ->where('PDF_TYPE', 'APPLICATION')
                ->orderBy('PDF_ID', 'DESC')
                ->get();
            $return_data->PDF_APP = $PDF;

            // dd(count($return_data->PDF_APP));
            if (count($return_data->PDF_APP) == 0) {
                $APPLICATION = DB::table('dbo.APPLICATION')
                    ->select('PERSON_ID', 'APP_ID', 'PRODUCT_ID')
                    ->where('APP_ID', $data['APP_ID'])
                    ->get();

                if (count($APPLICATION) != 0) {
                    $return_data->URL_APP['APP_ID'] = $data['APP_ID'];
                    $return_data->URL_APP['PERSION_ID'] = $APPLICATION[0]->PERSON_ID;
                    $return_data->URL_APP['PROD_ID'] = $APPLICATION[0]->PRODUCT_ID;
                }
            }

            return $return_data;
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()));
        }
    }
}
