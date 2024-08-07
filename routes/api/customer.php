<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post("register", [CustomerController::class, "register"]);

Route::post("login", [CustomerController::class, "login_api"]);

route::post("report", [ReportController::class, "report"]);

Route::group(["middleware" => "check:api-customer"], function () {

    Route::get("logout", [CustomerController::class, "logout_api"]);

    route::post("verify", [CustomerController::class, "apiVerify"]);

    Route::post("service",[CustomerController::class,"addService_api"]);

    Route::post("updateService",[CustomerController::class,"updateService"]);

    Route::get("deleteService/{service_id}",[CustomerController::class,"deleteService"]);

    Route::post("post" , [CustomerController::class , "post_api"]);

    Route::post("browse", [CustomerController::class, "browse"]);

    Route::post("follow", [CustomerController::class, "putFollow"]);

    Route::post("search", [CustomerController::class, "search"]);

    Route::post("filter", [CustomerController::class, "searchWithFilter"]);

    // Route::post("post",[CustomerController::class,"post_api"]);

    // Route::post("updatePost/{post_id}", [CustomerController::class, "updatePost_api"]);

    Route::get("deletePost/{post_id}", [CustomerController::class, "deletePost"]);

    Route::post("add-comment/{post_id}", [CustomerController::class, "addComment"]);

    Route::post("updateComment/{comment_id}", [CustomerController::class, "updateComment"]);

    Route::get("deleteComment/{comment_id}", [CustomerController::class, "deleteComment"]);

    Route::post("addLikeToPost", [CustomerController::class, "addLikeToPost"]);

    Route::post("unlikePost", [CustomerController::class, "unlikePost"]);

    Route::post("addLikeToComment", [CustomerController::class, "addLikeToComment"]);

    Route::post("unlikeComment", [CustomerController::class, "unlikeComment"]);

    Route::get("viewProfile/{type}/{id}", [CustomerController::class, "show"]);

    Route::post("updateProfile", [CustomerController::class, 'updateProfile']);

    Route::get("deleteAccount/{id}", [CustomerController::class, "deleteAccount"]);

});
