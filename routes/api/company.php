<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post("register",[CompanyController::class,"register"]);
    Route::post("login",[CompanyController::class,"login_api"]);
Route::group(["middleware"=> "check:api-company"],function(){
    Route::post("logout",[CompanyController::class,"logout_api"]);
});
