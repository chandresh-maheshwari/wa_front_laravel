<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\PackageController;
use App\Models\AccountInformationModel;
use App\Models\OrderModel;
use App\Models\TestimonialModel;
use App\Models\ProductModel;
use App\Models\PromocodeModel;
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


Route::get('/frontend_laravel' , function(){
     
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
Route::post('add', [ProductController::class, 'product_add'])->name('add');
Route::get('/product_update/{id}', [ProductController::class, 'product_update'])->name('product_update');
Route::post('update', [ProductController::class, 'update'])->name('update');
Route::get('/product_delete/{id}', [ProductController::class, 'product_delete'])->name('product_delete');
Route::get('/product_list', function () {
    return view('dashboard.product_list', ['data' => TestimonialModel::all()->sortByDesc("id")->where('is_deleted', 0)]);
});

Route::get('/promocode_add',[PromocodeController::class,'promocode_add'])->name('promocode_add');

// Route::get('promocode_add', function () {
//     return view('dashboard.promocode_add', ['data' => ProductModel::all()->sortByDesc("id")->where('is_deleted', 0)]);
// });
Route::post('promocode_add_data', [PromocodeController::class, 'promocode_add_data'])->name('promocode_add_data');
// Route::get('promocode_datatable', function () {
//     return view('dashboard.promocode_dt', [
//         'data' => PromocodeModel::all()->sortByDesc("id")->where('is_deleted', 0), 'product' => ProductModel::all()->sortByDesc("id")->where('is_deleted', 0),
//     ]);
// });
// listing promocode by datatable
Route::get('/promocode_datatable/{lang?}',[PromocodeController::class, 'promocode_datatable'])->name('promocode_datatable');
Route::get('/promocode_update/{id?}', [PromocodeController::class, 'promocode_update'])->name('promocode_update');
Route::post('update_promo', [PromocodeController::class, 'update_promo'])->name('update_promo');
Route::get('/promocode_delete/{id}', [PromocodeController::class, 'promocode_delete'])->name('prompcode_delete');

//=========================================================================
//                  Waste START  ------- FRONTEND
// ========================================================================
Route::get('/testionmonial',[TestimonialsController::class,'testionmonial'])->name('testionmonial');
Route::post('/store', [TestimonialsController::class, 'store'])->name('testionmonial.store');
Route::get('/list', [TestimonialsController::class, 'testimonial_list'])->name('testimonial_list');
Route::get('/show', [TestimonialsController::class, 'show'])->name('testionmonial.show');
Route::delete('/delete/{id?}', [TestimonialsController::class, 'destroy'])->name('testionmonial.destroy');
Route::get('/edit/{id?}', [TestimonialsController::class, 'edit'])->name('testionmonial.edit');
Route::get('/update', [TestimonialsController::class, 'update'])->name('testionmonial.update');
Route::post('/update-save', [TestimonialsController::class, 'update_save'])->name('testionmonial.update-save');






//=========================================================================
//                  Waste End  ------- FRONTEND
// ========================================================================

    Route::get('/client',[ClientController::class,'create'])->name('create');
    Route::post('/create', [ClientController::class, 'store'])->name('client.store');
    Route::get('/clist', [ClientController::class, 'client_list'])->name('client_list');
    Route::post('/listing', [ClientController::class, 'listing'])->name('client.listing');
    Route::post('/cdelete', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::get('/cedit/{id?}',[ClientController::class, 'edit'])->name('client.edit');
    Route::get('/cupdate', [ClientController::class, 'update'])->name('client.update');
    Route::post('/cupdate-save', [ClientController::class, 'update_save'])->name('client.update-save');


    Route::get('/services',[ServicesController::class,'create'])->name('create_service');
    Route::post('/services-store',[ServicesController::class,'store'])->name('services.store');
    Route::get('/service_list', [ServicesController::class, 'services_list'])->name('services_list');
    Route::post('/service_listing', [ServicesController::class, 'listing'])->name('services.listing');
    Route::post('/service_delete', [ServicesController::class, 'destroy'])->name('services.destroy');
    Route::get('/service_edit/{id?}',[ServicesController::class, 'edit'])->name('services.edit');
    Route::get('/service_update', [ServicesController::class, 'update'])->name('services.update');
    Route::post('/service_update_save', [ServicesController::class, 'update_save'])->name('services.update-save');







    Route::get('/packages',[PackageController::class,'create'])->name('package_create');
    Route::post('/packages-store',[PackageController::class,'store'])->name('package.store');
    Route::get('/package_list', [PackageController::class, 'package_list'])->name('package_list');
    Route::post('/package_listing', [PackageController::class, 'listing'])->name('package.listing');
    Route::post('/package_delete', [PackageController::class, 'destroy'])->name('package.destroy');
    Route::get('/package_edit/{id?}',[PackageController::class, 'edit'])->name('package.edit');
    Route::get('/package_update', [PackageController::class, 'update'])->name('package.update');
    Route::post('/package_update_save', [PackageController::class, 'update_save'])->name('package.update-save');


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

Route::post('/addaccountinfo', [UserAuthController::class, 'addaccountinformation'])->name('addaccountinformation');


Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/', [UserAuthController::class, 'pricing']);
Route::post('/pricechange',[UserAuthController::class,'pricechange'])->name('pricechange');
Route::post('/promoverify',[UserAuthController::class,'promoverify'])->name('promoverify');

Route::post('address', [UserAuthController::class, 'address'])->name('address');

Route::post('orderconfirm', [UserAuthController::class, 'orderconfirm'])->name('orderconfirm');

Route::post('paymentmethod', [OrderController::class, 'paymentmethod'])->name('paymentmethod');
Route::post('cardinformation', [OrderController::class, 'cardinformation'])->name('cardinformation');


Route::group(['middleware' => 'user'], function () {
    // Route::get('/', [UserController::class, 'index']);

    Route::get('/userlogout', [UserController::class, 'userlogout'])->name('userlogout');
});
Route::get('/homepage', function(){
    return view('homepage');
});
Route::post('/loginuser', [UserAuthController::class, 'loginuser'])->name('loginuser');

//=========================================================================
//                  USER middleware END  ------- FRONTEND 
// ========================================================================