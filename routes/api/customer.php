<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
Route::post("logout", [CustomerController::class, "logout_api"]);
Route::group(["middleware" => "check:api-customer"], function () {

});
