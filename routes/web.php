<?php

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Monolog\Processor\HostnameProcessor;

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

//=========================================================================
//                  auth middleware START  
// ========================================================================
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    });
});


Route::get('/frontend_laravel', function () {

    return view("home");
});
//=========================================================================
//                  auth middleware END  
// ========================================================================

Auth::routes();

Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/product_add', function () {
    return view('dashboard.product_add');
});

//=========================================================================
//                  USER middleware START  ------- FRONTEND
// ========================================================================
Route::get('/signin', function () {
    if (Session::has('userid')) {
        return redirect('/');
    }
    return view('signin');
})->name('signin');

// Route::get('/userlogin', [UserController::class], 'signin')->name('signin');

// Route::post('/addaccountinfo', [UserAuthController::class, 'addaccountinformation'])->name('addaccountinformation');


Route::get('/register', function () {
    return view('register');
})->name('register');

// Route::get('/', [UserAuthController::class, 'pricing']);
// Route::post('/pricechange', [UserAuthController::class, 'pricechange'])->name('pricechange');
// Route::post('/promoverify', [UserAuthController::class, 'promoverify'])->name('promoverify');

// Route::post('address', [UserAuthController::class, 'address'])->name('address');

// Route::post('orderconfirm', [UserAuthController::class, 'orderconfirm'])->name('orderconfirm');

Route::group(['middleware' => 'user'], function () {
    // Route::get('/', [UserController::class, 'index']);

    Route::get('/userlogout', [UserController::class, 'userlogout'])->name('userlogout');
});
Route::get('/homepage', function () {
    return view('homepage');
});
Route::post('/loginuser', [UserAuthController::class, 'loginuser'])->name('loginuser');

//=========================================================================
//                  USER middleware END  ------- FRONTEND 
// ========================================================================