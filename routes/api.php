<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API_Service_SMS;
use App\Http\Controllers\API_Support_K2;
use App\Http\Controllers\Login_Auth_Controller;
use App\Http\Controllers\API_SCB;
use App\Http\Controllers\API_Sandbox_SCB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API SUPPORT K2
Route::post('/K2_Add_Signature', [API_Support_K2::class, 'Add_Signature']);



// API SMS POST

Route::post('/Login_Auth', [Login_Auth_Controller::class, 'Get_Token']);


Route::group(['middleware' => ['JWT_Token']], function () {

    Route::get('/Send_SMS', [API_Service_SMS::class, 'Send_SMS']);


    Route::any('/SMS_send_ByType', [API_Service_SMS::class, 'SMS_send_ByType']);


    // Garantor
    // Route::get('/send_SMS_Garantor', [API_Service_SMS::class, 'submit_send_SMS_Garantor']);


    // Welcome Call
    Route::get('/send_SMS_WelcomeCall', [API_Service_SMS::class, 'submit_send_SMS_WelcomeCall']);

});


// API SCB
// Route::post('/Payment_Confirm', [API_Sandbox_SCB::class, 'SCB_Callback_Payment_Confirm']);
Route::post('/Payment_Confirm', [API_SCB::class, 'SCB_Callback_Payment_Confirm']);