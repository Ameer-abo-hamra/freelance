<?php

use App\Http\Controllers\JobSeekerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

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

route::get("csrf", function () {
    return csrf_token();
});

Route::post("register", [JobSeekerController::class, "register"]);

Route::post("login", [JobSeekerController::class, "login"]);

Route::get("test", [JobSeekerController::class, "apply_"]);

route::post("report", [ReportController::class, "report"]);

Route::group(["middleware" => "check:web-job_seeker"], function () {

    Route::post("verify", [JobSeekerController::class, "verifyWeb"])->name("verify");

    Route::get("resend-verify", [JobSeekerController::class, "resend"]);

    Route::get("logout", [JobSeekerController::class, "logout"]);

    Route::post("apply", [JobSeekerController::class, "applyWeb"]);

    Route::post("post", [JobSeekerController::class, "postWeb"]);

    Route::post("browse", [JobSeekerController::class, "browse"]);

    Route::post("follow", [JobSeekerController::class, "putFollow"]);

    Route::post("add-comment/{post_id}", [JobSeekerController::class, "addComment_web"]);

    Route::post("updateComment/{comment_id}", [JobSeekerController::class, "updateComment"]);

    Route::get("deleteComment/{comment_id}", [JobSeekerController::class, "deleteComment"]);

    Route::post("addLikeToPost", [JobSeekerController::class, "addLikeToPost"]);

    Route::post("unlikePost", [JobSeekerController::class, "unlikePost"]);

    Route::post("addLikeToComment", [JobSeekerController::class, "addLikeToComment"]);

    Route::post("add-comment", [JobSeekerController::class, "addComment"]);

    Route::post("unlikeComment", [JobSeekerController::class, "unlikeComment"]);

});

