<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\PageLogin_Controller;
use App\Http\Controllers\Cookie_Controller;

use App\Http\Controllers\API_Service_SMS;
use App\Http\Controllers\API_Service_Mail;

use App\Http\Controllers\Admin_Dashbord;
use App\Http\Controllers\Admin_Detail_SMS;


use App\Http\Controllers\Pthread;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::fallback(function () {
//     return view('Template.Page404');
// });

Route::get('/login', function () {
    // dd(Cookie::get('SMS_Username_server'));
    if (Cookie::get('SMS_Username_server') != null) {
        // dd(Cookie::get('SMS_Username_server'));
        return redirect()->route('homepage');
    } else {
        Cookie::queue(Cookie::forget('SMS_Username_server'));
        return view('login');
    }
})->name('login');


Route::post('/Login_user', [PageLogin_Controller::class, 'Login_user']);


Route::get('/logout', function () {
    Cookie::queue(Cookie::forget('SMS_Username_server'));
    // Cookie::queue(Cookie::forget('SMS_Username_Permission'));
    return redirect()->route('login');
})->name('logout');


Route::group(['middleware' => ['authLogin']], function () {
    Route::get('/', function () {
        return view('Admin_Dashbord');
    })->name('homepage');

    Route::get('profile', function () {
        return view('Profile');
    });

    Route::get('Map', function () {
        return view('Map');
    });

    Route::get('Detail_Send_SMS_bill', function () {
        return view('Detail_Send_SMS_bill');
    });

    Route::group(['middleware' => ['authAdmin']], function () {
        
    });
});



Route::post('/get_cookie', [Cookie_Controller::class, 'Get_cookieByName']);

Route::post('/SMS_Sender', [Admin_Dashbord::class, 'check_sender']);

Route::post('/SMS_Sender_type', [Admin_Dashbord::class, 'check_sender_type']);

Route::post('/list_sms', [Admin_Detail_SMS::class, 'list_sms']);

Route::post('/SMS_Detail', [Admin_Detail_SMS::class, 'SMS_Detail']);


Route::get('page_404', function () {
    return view('Error/Page404');
});

// Route::get('page_403', function () {
//     return view('Error/Page403');
// });


// SMS Mailbit API
Route::get('/SMS_Check_Credit', [API_Service_SMS::class, 'SMS_Check_Credit']);


Route::middleware(['basicAuth'])->group(function () {
    //All the routes are placed in here
    Route::any('/send_SMS_Invoice', [API_Service_SMS::class, 'submit_send_SMS_Invoice']);
});

Route::get('/test_send_SMS', [API_Service_SMS::class, 'test_send_SMS']);



// Mail Nipamail API
Route::get('/test_Mail', [API_Service_Mail::class, 'PostRequest_Mail']);

// Route::get('/conf_sms', [API_Service_Mail::class, 'conf_sms_send']);




////////////////////////////////////////////////////////////////////////////

Route::get('index', function () {
    return view('index');
});


Route::get('/count', [Admin_Detail_SMS::class, 'test_b']);

