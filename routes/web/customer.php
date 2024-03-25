<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Traits\Response;
use Illuminate\Support\Facades\Mail;

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

Route::post("register", [CustomerController::class, "register"]);

route::get("login", function () {
    return view("login");
});
route::post("login", [CustomerController::class, "login"])->name("login");

Route::group(["middleware" => ["check:customer"]], function () {

    Route::get("logout", [CustomerController::class, "logout"]);
});
