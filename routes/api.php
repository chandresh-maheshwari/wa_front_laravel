<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactPageController;
use Illuminate\Http\Request;
use App\Http\Controllers\DynamicPostController;
use App\Http\Controllers\GetFormDataController;
use App\Http\Controllers\GetPageFormDataController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostStoreController;
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

   
    /** Pages api routes create by ns */

    Route::get('/page-list', [PageController::class, 'index']);
    Route::post('/page-store', [PageController::class, 'store']);
    Route::get('/page-show/{id}', [PageController::class, 'show']);
    Route::get('/page-edit/{id}', [PageController::class, 'edit']);
    Route::post('/page-update/{id}', [PageController::class, 'update']);
    Route::delete('/page-delete/{id}', [PageController::class, 'destroy']);
    Route::post('/page-active/{id}', [PageController::class, 'active']);
    Route::post('/page-status/{id}', [PageController::class, 'pageStatus']);

    /** Dynamic Post api routes create by ns */

    Route::get('/dynamic-post-list', [DynamicPostController::class, 'listPosts']);
    Route::post('/dynamic-post-store', [DynamicPostController::class, 'addPost']);
    Route::get('/dynamic-post-show/{id}', [DynamicPostController::class, 'show']);
    Route::get('/dynamic-post-edit/{id}', [DynamicPostController::class, 'edit']);
    Route::post('/dynamic-post-update/{id}', [DynamicPostController::class, 'update']);
    Route::delete('/dynamic-post-delete/{id}', [DynamicPostController::class, 'destroy']);
    Route::post('/dynamic-post-active/{id}', [DynamicPostController::class, 'active']);


    /**  post value store api routes create by ns*/

    Route::get('/post-data-list/{postName}', [PostStoreController::class, 'getList']);
    Route::post('/post-data-store/{postTitle}', [PostStoreController::class, 'postStore']);
    Route::get('/post-data-show/{postName}', [PostStoreController::class, 'show']);
    Route::get('/post-data-edit/{id}', [PostStoreController::class, 'edit']);
    Route::post('/post-data-update/{id}', [PostStoreController::class, 'update']);
    Route::delete('/post-data-delete/{id}', [PostStoreController::class, 'destroy']);
    Route::post('/post-data-active/{id}', [PostStoreController::class, 'active']);

    /** Get Form data value api routes create by ns */
    Route::get('/get-form-data/{postTitle}', [GetFormDataController::class, 'getFormData']);


    /** Get Form data value api routes create by ns */
    Route::get('/get-page-data/{pageName}', [GetPageFormDataController::class, 'getpageData']);


      /**  Contact Page api routes create by ns*/

      Route::get('/contact-page-list', [ContactPageController::class, 'index']);
      
      Route::delete('/contact-page-delete/{id}', [ContactPageController::class, 'destroy']);

   
});

/** Login api routes by ns */

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logoutpage', [LoginController::class, 'logoutpage']);
Route::post('/refresh', [LoginController::class, 'refresh']);

/** Forget password api routes by ns */

Route::post('/send-otp', [LoginController::class, 'sendOtp']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);

Route::get('/page/{pageName}', [PageController::class, 'showByPageName']);

/** get page data with his post store with ordering */

Route::get('/pages', [PageController::class, 'showAllPagesWithPostStores']);

/** contact page store api  */
Route::post('/contact-page-store', [ContactPageController::class, 'store']);

/** this route used for the if status is active that get only page name  */

Route::get('/page-status-data', [PageController::class, 'getActivePageData']);