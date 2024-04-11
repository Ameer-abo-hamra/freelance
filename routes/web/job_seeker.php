<?php

use App\Http\Controllers\JobSeekerController;
use Illuminate\Support\Facades\Route;

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

Route::group(["middleware" => "check:web-job_seeker"], function () {

    Route::post("verify", [JobSeekerController::Class, "verify"])->name("verify");

    Route::get("resend-verify", [JobSeekerController::class, "resend"]);

    Route::get("logout", [JobSeekerController::class, "logout"]);

    Route::post("progress", [JobSeekerController::class, "progress"]);
});
