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

    Route::get("get-applicants/{service_id}" , [CustomerController::Class , "getApplicants"]);

    Route::post("updateService",[CustomerController::class,"updateService"]);

    Route::get("deleteService/{service_id}",[CustomerController::class,"deleteService"]);

    Route::post("add_post" , [CustomerController::class , "post_api"]);

    Route::get("getAuthorOfPost/{postId}",[CustomerController::class,"getAuthorOfPost"]);

    Route::post("browse", [CustomerController::class, "browse"]);

    Route::post("follow", [CustomerController::class, "putFollow"]);

    Route::post("search", [CustomerController::class, "search"]);

    Route::post("filter", [CustomerController::class, "searchWithFilter"]);

    Route::post("updatePost/{post_id}", [CustomerController::class, "updatePost_api"]);

    Route::get("deletePost/{post_id}", [CustomerController::class, "deletePost"]);

    Route::post("add-comment/{post_id}", [CustomerController::class, "addComment"]);

    Route::post("updateComment/{comment_id}", [CustomerController::class, "updateComment"]);

    Route::get("deleteComment/{comment_id}", [CustomerController::class, "deleteComment"]);

    Route::post("addLikeToPost", [CustomerController::class, "addLikeToPost_api"]);

    Route::post("unlikePost", [CustomerController::class, "unlikePost_api"]);

    Route::post("addLikeToComment", [CustomerController::class, "addLikeToComment_api"]);

    Route::post("unlikeComment", [CustomerController::class, "unlikeComment_api"]);

    Route::get("viewProfile/{type}/{id}", [CustomerController::class, "show"]);

    Route::post("updateProfile", [CustomerController::class, 'updateProfile']);

    Route::get("deleteAccount/{id}", [CustomerController::class, "deleteAccount"]);

    Route::get("showServiceAppliers/{serviceId}",[CustomerController::class,"showServiceAppliers"]);

    Route::post("acceptServiceApplier/{serviceId}",[CustomerController::class,"acceptServiceApplier"]);

    Route::get("markServiceAsDone/{serviceId}",[CustomerController::class,"markServiceAsDone"]);

    Route::post("message" , [CustomerController::class , "messageApi"]);

    Route::get("countOfComments/{post_id}",[CustomerController::class,"commentsCount"]);

    Route::get("countOfLikes/{post_id}",[CustomerController::class,"likesCount"]);

    Route::get("commentslist/{post_id}",[CustomerController::class,"commentslist"]);

    Route::post("viewProfile" , [CustomerController::class , "showProfile"]);

});
