<?php

use App\Http\Controllers\AuthController;
// use App\Http\Controllers\precontroller;
use App\Models\User;
use Illuminate\Http\Request;


use App\Http\Controllers\ClientController;
use App\Http\Controllers\PackageController;
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

// Route::get('/users', function () {
//     $users = User::get();
//     return response()->json($users);
// });

// Route::group([
//     'prefix' => 'auth',
// ], function ($router) {
//     Route::post('/login', [UserAuthController::class, 'login']);
//     Route::post('/logout', [UserAuthController::class, 'logout']);
//     Route::post('/refresh', [UserAuthController::class, 'refresh']);
// });

Route::post("/add", [App\Http\Controllers\precontroller::class, 'add']);

// login api
Route::post("/login", [App\Http\Controllers\precontroller::class, 'login']);
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


/** top menu api routes */
Route::get('/menu', [TopMenuController::class, 'index']);
Route::post('/store', [TopMenuController::class, 'store']);
Route::get('/menuedit/{id}', [TopMenuController::class, 'edit']);
Route::post('/update/{id}', [TopMenuController::class, 'updateMenu']);
Route::get('/show/{id}', [TopMenuController::class, 'show']);
Route::delete('/delete/{id}', [TopMenuController::class, 'destroy']);


/** Quote section api routes by ns */
Route::get('/quote-section-list', [QuoteController::class, 'index']);
Route::post('/quote-store', [QuoteController::class, 'store']);
Route::get('/quote-show/{id}', [QuoteController::class, 'show']);
Route::get('/quote-edit/{id}', [QuoteController::class, 'edit']);
Route::post('/quote-update/{id}', [QuoteController::class, 'updateQuoteSection']);
Route::delete('/quote-delete/{id}', [QuoteController::class, 'destroy']);