<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChooseWasteAccountController;
// use App\Http\Controllers\precontroller;
use App\Models\User;
use Illuminate\Http\Request;


use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\DynamicPageController;
use App\Http\Controllers\DynamicPostController;
use App\Http\Controllers\GetFormDataController;
use App\Http\Controllers\NavBarController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ImproveEnvirmentalPortectionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostStoreController;
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
    Route::post('/navbar-active/{id}', [NavBarController::class, 'active']);

    /** Home Page api routes by ns */

    Route::get('/home-page', [HomePageController::class, 'index']);
    Route::post('/home-page-store', [HomePageController::class, 'store']);
    Route::get('/home-page-edit/{id}', [HomePageController::class, 'edit']);
    Route::post('/home-page-update/{id}', [HomePageController::class, 'updateHomePage']);
    Route::get('/home-page-show/{id}', [HomePageController::class, 'show']);
    Route::delete('/home-page-delete/{id}', [HomePageController::class, 'destroy']);
    Route::post('/home-page-active/{id}', [HomePageController::class, 'active']);

    /** Producer & Receiver apis routes by ns */

    Route::get('/producer-receiver-list', [ProducerReceiverController::class, 'index']);
    Route::post('/producer-receiver-store', [ProducerReceiverController::class, 'store']);
    Route::get('/producer-receiver-show/{id}', [ProducerReceiverController::class, 'show']);
    Route::get('/producer-receiver-edit/{id}', [ProducerReceiverController::class, 'edit']);
    Route::post('/producer-receiver-update/{id}', [ProducerReceiverController::class, 'update']);
    Route::delete('/producer-receiver-delete/{id}', [ProducerReceiverController::class, 'destroy']);
    Route::post('/producer-receiver-active/{id}', [ProducerReceiverController::class, 'active']);

    /** Quote section api routes by ns */

    Route::get('/quote-section-list', [QuoteController::class, 'index']);
    Route::post('/quote-store', [QuoteController::class, 'store']);
    Route::get('/quote-show/{id}', [QuoteController::class, 'show']);
    Route::get('/quote-edit/{id}', [QuoteController::class, 'edit']);
    Route::post('/quote-update/{id}', [QuoteController::class, 'updateQuoteSection']);
    Route::delete('/quote-delete/{id}', [QuoteController::class, 'destroy']);

    /** Choose waste accountant api routes by ns */

    Route::get('/choose-waste-accountant-list', [ChooseWasteAccountController::class, 'index']);
    Route::post('/choose-waste-accountant-store', [ChooseWasteAccountController::class, 'store']);
    Route::get('/choose-waste-accountant-show/{id}', [ChooseWasteAccountController::class, 'show']);
    Route::get('/choose-waste-accountant-edit/{id}', [ChooseWasteAccountController::class, 'edit']);
    Route::post('/choose-waste-accountant-update/{id}', [ChooseWasteAccountController::class, 'update']);
    Route::delete('/choose-waste-accountant-delete/{id}', [ChooseWasteAccountController::class, 'destroy']);
    Route::post('/choose-waste-accountant-active/{id}', [ChooseWasteAccountController::class, 'active']);

    /** Improve Envirmental Protection api routes by ns */

    Route::get('/improve-envirmental-protection-list', [ImproveEnvirmentalPortectionController::class, 'index']);
    Route::post('/improve-envirmental-protection-store', [ImproveEnvirmentalPortectionController::class, 'store']);
    Route::get('/improve-envirmental-protectiont-show/{id}', [ImproveEnvirmentalPortectionController::class, 'show']);
    Route::get('/improve-envirmental-protectiont-edit/{id}', [ImproveEnvirmentalPortectionController::class, 'edit']);
    Route::post('/improve-envirmental-protectiont-update/{id}', [ImproveEnvirmentalPortectionController::class, 'update']);
    Route::delete('/improve-envirmental-protectiont-delete/{id}', [ImproveEnvirmentalPortectionController::class, 'destroy']);
    Route::post('/improve-envirmental-protectiont-active/{id}', [ImproveEnvirmentalPortectionController::class, 'active']);

    /** About Us page api routes create by ns */

    Route::get('/about-us-list', [AboutUsController::class, 'index']);
    Route::post('/about-us-store', [AboutUsController::class, 'store']);
    Route::get('/about-us-show/{id}', [AboutUsController::class, 'show']);
    Route::get('/about-us-edit/{id}', [AboutUsController::class, 'edit']);
    Route::post('/about-us-update/{id}', [AboutUsController::class, 'update']);
    Route::delete('/about-us-delete/{id}', [AboutUsController::class, 'destroy']);
    Route::post('/about-us-active/{id}', [AboutUsController::class, 'active']);

    /** Contact page detail api routes create by ns */

    Route::get('/contact-page-list', [ContactPageController::class, 'index']);
    Route::post('/contact-page-store', [ContactPageController::class, 'store']);
    Route::get('/contact-page-show/{id}', [ContactPageController::class, 'show']);
    Route::get('/contact-page-edit/{id}', [ContactPageController::class, 'edit']);
    Route::post('/contact-page-update/{id}', [ContactPageController::class, 'update']);
    Route::delete('/contact-page-delete/{id}', [ContactPageController::class, 'destroy']);
    Route::post('/contact-page-active/{id}', [ContactPageController::class, 'active']);

    /** Posts page api routes create by ns */

    Route::get('/post-page-list', [PostsController::class, 'index']);
    Route::post('/post-page-store', [PostsController::class, 'store']);
    Route::get('/post-page-show/{id}', [PostsController::class, 'show']);
    Route::get('/post-page-edit/{id}', [PostsController::class, 'edit']);
    Route::post('/post-page-update/{id}', [PostsController::class, 'update']);
    Route::delete('/post-page-delete/{id}', [PostsController::class, 'destroy']);
    Route::post('/post-page-active/{id}', [PostsController::class, 'active']);

    /** Pages api routes create by ns */

    Route::get('/page-list', [PageController::class, 'index']);
    Route::post('/page-store', [PageController::class, 'store']);
    Route::get('/page-show/{id}', [PageController::class, 'show']);
    Route::get('/page-edit/{id}', [PageController::class, 'edit']);
    Route::post('/page-update/{id}', [PageController::class, 'update']);
    Route::delete('/page-delete/{id}', [PageController::class, 'destroy']);
    Route::post('/page-active/{id}', [PageController::class, 'active']);

    /** Dynamic Post api routes create by ns */

    Route::get('/dynamic-post-list', [DynamicPostController::class, 'listPosts']);
    Route::post('/dynamic-post-store', [DynamicPostController::class, 'addPost']);
    Route::get('/dynamic-post-show/{id}', [DynamicPostController::class, 'show']);
    Route::get('/dynamic-post-edit/{id}', [DynamicPostController::class, 'edit']);
    Route::post('/dynamic-post-update/{id}', [DynamicPostController::class, 'update']);
    Route::delete('/dynamic-post-delete/{postTitle}', [DynamicPostController::class, 'destroy']);
    Route::post('/dynamic-post-active/{postTitle}', [DynamicPostController::class, 'active']);


    /**  post value store api routes create by ns*/

    Route::get('/post-data-list/{postName}', [PostStoreController::class, 'getList']);
    Route::post('/post-data-store/{postTitle}', [PostStoreController::class, 'postStore']);
    Route::get('/post-data-show/{postName}', [PostStoreController::class, 'show']);
    Route::get('/post-data-edit/{postName}', [PostStoreController::class, 'edit']);
    Route::post('/post-data-update/{postName}', [PostStoreController::class, 'update']);
    Route::delete('/post-data-delete/{id}', [PostStoreController::class, 'destroy']);
    Route::post('/post-data-active/{id}', [PostStoreController::class, 'active']);




    /** Get Form data value api routes create by ns */
    Route::get('/get-form-data/{postTitle}', [GetFormDataController::class, 'getFormData']);


    /** Dynamic page api routes create by ns */
    Route::get('/dynamic-page-list', [DynamicPageController::class, 'listPages']);
    Route::post('/dynamic-page-store', [DynamicPageController::class, 'addPage']);
    Route::get('/dynamic-page-show/{id}', [DynamicPageController::class, 'show']);
    Route::get('/dynamic-page-edit/{id}', [DynamicPageController::class, 'edit']);
    Route::post('/dynamic-page-update/{id}', [DynamicPageController::class, 'update']);
    Route::delete('/dynamic-page-delete/{pageName}', [DynamicPageController::class, 'destroy']);
    Route::post('/dynamic-page-active/{pageName}', [DynamicPageController::class, 'active']);

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

/** Login api routes by ns */

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logoutpage', [LoginController::class, 'logoutpage']);
Route::post('/refresh', [LoginController::class, 'refresh']);

/** Forget password api routes by ns */

Route::post('/send-otp', [LoginController::class, 'sendOtp']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
