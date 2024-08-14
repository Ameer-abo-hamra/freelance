<?php

use App\Events\ForTesting;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportController;
use App\Models\Company;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

route::get("csrf", function () {
    return csrf_token();
});

route::post("register", [CompanyController::class, "register"]);

route::post("login", [CompanyController::class, "login"]);

// Route::get("delete-account/{id}", [CompanyController::class, "deleteAccount"]);

route::post("report", [ReportController::class, "report"]);

Route::group(["middleware" => "check:web-company"], function () {

    Route::get("logout", [CompanyController::class, "log_out"]);

    Route::post("verify", [CompanyController::class, "verify"])->name("verify");

    Route::get("resend-verify", [CompanyController::class, "resend"]);

    Route::get("category", [CompanyController::class, "getCategory"]);

    Route::get("types/{categoryy_id}", [CompanyController::class, "getTypesSkills"]);

    Route::get("skills/{type_name}", [CompanyController::class, "getSkillName"]);

    Route::post("addoffer", [CompanyController::class, "addOfferWeb"]);

    Route::post("update-offer", [CompanyController::class, "offerUpdate"]);

    Route::get("get-offers/{Company_id}", [CompanyController::class, "getOffers"]);

    Route::get("get-job-applicants/{offer_id}", [CompanyController::class, "getJobApplicants"]);

    Route::post("change-offer-state", [CompanyController::class, "ChangeOfferStateWeb"]);

    Route::post("add_post", [CompanyController::class, "postWeb"]);

    Route::post("updatePost/{post_id}", [CompanyController::class, "updatePost_web"]);

    Route::get("deletePost/{post_id}", [CompanyController::class, "deletePost"]);

    Route::post("follow", [CompanyController::class, "putFollow"]);

    Route::post("/browse", [CompanyController::class, "browse"]);

    Route::post("add-comment/{post_id}", [CompanyController::class, "addComment_web"]);

    Route::post("updateComment/{comment_id}", [CompanyController::class, "updateComment"]);

    Route::get("deleteComment/{comment_id}", [CompanyController::class, "deleteComment"]);

    Route::post("addLikeToPost", [CompanyController::class, "addLikeToPost_web"]);

    Route::post("unlikePost", [CompanyController::class, "unlikePost_web"]);

    Route::post("addLikeToComment", [CompanyController::class, "addLikeToComment_web"]);

    Route::post("unlikeComment", [CompanyController::class, "unlikeComment_web"]);

    Route::post("search", [CompanyController::class, "search"]);

    Route::post("filter", [CompanyController::class, "searchWithFilter"]);

    Route::post("viewProfile", [CompanyController::class, "show"]);

    Route::post("updateProfile", [CompanyController::class, 'updateProfile_web']);

    Route::get("deleteAccount/{id}", [CompanyController::class, "deleteAccount"]);

    Route::post("applyService", [CompanyController::class, "applyServiceWeb"]);

    Route::post("message", [CompanyController::class, "messageWeb"]);
});

Route::get("test", function () {
    return view("test");
});

