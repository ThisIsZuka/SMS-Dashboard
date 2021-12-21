<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\PageLogin_Controller;



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
    // dd(request()->cookie('SMS_Username_server'));
    if(request()->cookie('SMS_Username_server') == null){
        return view('login');
    }else{
        return redirect()->route('home');
    }
    
})->name('login');


Route::post('/Login_user', [PageLogin_Controller::class, 'Login_user']);


Route::get('/logout', function () {
    Cookie::queue(Cookie::forget('SMS_Username_server'));
    return redirect()->route('login');
})->name('logout');


Route::group(['middleware' => ['authLogin']], function () {
    Route::get('/', function () {
        return view('Admin_Dashbord');
    })->name('home');

    Route::get('profile', function () {
        return view('Profile');
    });

});


Route::get('page_404', function () {
    return view('Template/Page404');
});
