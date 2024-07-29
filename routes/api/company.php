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

Route::group(["middleware"=>"check:api-company"] , function (){

    Route::get("logout", [CompanyController::class , "logout_api"]);

    route::post("verify" , [CompanyController::class , "apiVerify"]);

    Route::post("add-offer" , [CompanyController::Class , "addOfferApi"]);

    Route::post("update-offer",[CompanyController::class , "offerUpdate"]);

    Route::get("get-offers/{company_id}" , [CompanyController::Class , "getOffers"]);

    Route::get("get-job-applicants/{offer_id}" , [CompanyController::class , "getJobApplicants"]);

    Route::post("change-offer-state", [CompanyController::class, "ChangeOfferStateApi"]);

    Route::post("post",[CompanyController::Class , "postApi"]);

    Route::post("follow", [CompanyController::class, "putFollow"]);

    Route::post("/browse", [CompanyController::class, "browse"]);

    Route::post("add-comment", [CompanyController::class, "addComment"]);

});
