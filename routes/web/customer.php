<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Traits\Response;
use Illuminate\Support\Facades\Mail;
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


Route::get("csrf", function () {
    return response()->json([
        "status" => true,
        "csrf" => csrf_token()
    ]);
});

Route::post("register", [CustomerController::class, "register"])->name("register");
route::post("login", [CustomerController::class, "login"])->name("login");
route::post("report", [ReportController::class, "report"]);

Route::group(["middleware" => ["check:customer"]], function () {

    Route::get("logout", [CustomerController::class, "logout"]);

    Route::post("verify", [CustomerController::class, "verify"])->name("verify");

    Route::get("resend-verify", [CustomerController::class, "resend"]);

    Route::post("service", [CustomerController::class, "addService"]);

    Route::group(["middleware" => "active:customer"], function () {

    });

    Route::post("browse", [CustomerController::class, "browse"]);

    Route::post("follow", [CustomerController::class, "putFollow"]);

});
