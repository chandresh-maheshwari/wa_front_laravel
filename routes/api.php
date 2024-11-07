<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\precontroller;
use App\Models\User;
use Illuminate\Http\Request;


use App\Http\Controllers\ClientController;
use App\Http\Controllers\NavBarController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProducerReceiverController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\TopMenuController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    // Route::get('/quote/testing', [QuoteController::class, 'testing']);
   });


Route::post("/add", [App\Http\Controllers\precontroller::class, 'add']);

// Route::get("/userlist", [App\Http\Controllers\precontroller::class, 'userlist']);
Route::post("/userstore", [App\Http\Controllers\precontroller::class, 'userstore']);
// login api end

// all search api
Route::get('search/{name}', [App\Http\Controllers\CommansearchController::class, 'search']);
// all search api end

// Services all api
Route::post("/Services", [App\Http\Controllers\ServicesController::class, 'store']);
Route::get("/serviceslist", [App\Http\Controllers\ServicesController::class, 'Servicelist']);
Route::delete('Servicesdestroy/{id}', [App\Http\Controllers\ServicesController::class, 'destroy']);
Route::get('serviceedit/{id}', [App\Http\Controllers\ServicesController::class, 'edit']);
Route::put('update/{id}', [App\Http\Controllers\ServicesController::class, 'hardik']);
Route::post('/delete-clients/{id}', [App\Http\Controllers\ServicesController::class, 'destroydata']);
// Services all api  end

// Testimonial all api
Route::post("/Testimonial", [App\Http\Controllers\TestimonialsController::class, 'store']);
Route::get("/Testimoniallist", [App\Http\Controllers\TestimonialsController::class, 'Testimoniallist']);
Route::delete('destroy/{id}', [App\Http\Controllers\TestimonialsController::class, 'destroy']);
Route::get('Testimonialedit/{id}', [App\Http\Controllers\TestimonialsController::class, 'Testimonialedit']);
Route::put('testimonialupdate/{id}', [App\Http\Controllers\TestimonialsController::class, 'testimonialupdate']);
Route::post('/delete-Testimonial/{id}', [App\Http\Controllers\TestimonialsController::class, 'Testimonialdestroydata']);
// Testimonial all api end



//sfffffffffffffffffffffffffffffffffffffffffffffffffffffffff

Route::post('client', [ClientController::class, 'index']);
Route::get('client/{id}', [ClientController::class, 'show']);
Route::post('addnew', [ClientController::class, 'store']);
Route::get('list', [ClientController::class, 'lists']);
Route::get('/clients/{id}', [ClientController::class, 'getClient']);

Route::put('/product/{id}/update', [ClientController::class, 'updateclient']);
Route::post('/delete-clients', [ClientController::class, 'destroy']);

Route::delete('destroy/{id}', [ClientController::class, 'destroy']);

Route::delete('/delete-clients/{ids}', [ClientController::class, 'destroydata']);





// Package Page Route

Route::post('package', [PackageController::class, 'store']);
Route::get('lists', [PackageController::class, 'package_list']);
Route::get('/packages/{id}', [PackageController::class, 'getClient']);

Route::put('package/{id}', [PackageController::class, 'update_save']);

Route::delete('delete/{id}', [PackageController::class, 'destroydatas']);

Route::delete('/delete-clients/{ids}', [PackageController::class, 'destroy_all_data']);


/** top menu api routes by ns*/

Route::get('/top-menu', [TopMenuController::class, 'index']);
Route::post('/top-menu-store', [TopMenuController::class, 'store']);
Route::get('/top-menu-edit', [TopMenuController::class, 'edit']);
Route::post('/top-menu-update', [TopMenuController::class, 'updateMenu']);
Route::get('/top-menu-show/{id}', [TopMenuController::class, 'show']);
Route::delete('/top-menu-delete/{id}', [TopMenuController::class, 'destroy']);
Route::post('/top-menu-active/{id}', [TopMenuController::class, 'active']);


/** Nav bar api routes by ns */

Route::get('/navbar', [NavBarController::class, 'index']);
Route::post('/navbar-store', [NavBarController::class, 'store']);
Route::get('/narbar-show/{id}', [NavBarController::class, 'show']);
Route::get('/navbar-edit/{id}', [NavBarController::class, 'edit']);
Route::post('/navbar-update/{id}', [NavBarController::class, 'updateNavbar']);
Route::delete('/navbar-delete/{id}', [NavBarController::class, 'destroy']);


/** Home Page api routes by ns */

Route::get('/home-page', [HomePageController::class, 'index']);
Route::post('/home-page-store', [HomePageController::class, 'store']);
Route::get('/home-page-edit/{id}', [HomePageController::class, 'edit']);
Route::post('/home-page-update/{id}', [HomePageController::class, 'updateHomePage']);
Route::get('/home-page-show/{id}', [HomePageController::class, 'show']);
Route::delete('/home-page-delete/{id}', [HomePageController::class, 'destroy']);

/** Producer & Receiver apis routes by ns */

Route::get('/producer-receiver-list', [ProducerReceiverController::class, 'index']);
Route::post('/producer-receiver-store', [ProducerReceiverController::class, 'store']);
Route::get('/producer-receiver-show/{id}', [ProducerReceiverController::class, 'show']);
Route::get('/producer-receiver-edit/{id}', [ProducerReceiverController::class, 'edit']);
Route::post('/producer-receiver-update/{id}', [ProducerReceiverController::class, 'update']);
Route::delete('/producer-receiver-delete/{id}', [ProducerReceiverController::class, 'destroy']);

/** Quote section api routes by ns */

Route::get('/quote-section-list', [QuoteController::class, 'index']);
Route::post('/quote-store', [QuoteController::class, 'store']);
Route::get('/quote-show/{id}', [QuoteController::class, 'show']);
Route::get('/quote-edit/{id}', [QuoteController::class, 'edit']);
Route::post('/quote-update/{id}', [QuoteController::class, 'updateQuoteSection']);
Route::delete('/quote-delete/{id}', [QuoteController::class, 'destroy']);


/** Login api routes by ns */

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logoutpage', [LoginController::class, 'logoutpage']);
Route::post('/refresh', [LoginController::class, 'refresh']);

