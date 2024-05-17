<?php

use App\Events\ForTesting;
use App\Http\Controllers\CompanyController;
use App\Models\Company;
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

Route::get('/', function () {
    return view('welcome');
});

route::get("csrf", function () {
    return csrf_token();
});


Route::get("testws", function () {

    broadcast(new ForTesting("hi there "));
});
route::post("register", [CompanyController::class, "register"]);

route::post("login", [CompanyController::class, "login"]);

Route::group(["middleware" => "check:web-company"], function () {

    Route::get("logout", [CompanyController::class, "log_out"]);

    Route::post("verify", [CompanyController::class, "verify"])->name("verify");

    Route::get("resend-verify", [CompanyController::class, "resend"]);

    Route::get("category", [CompanyController::class, "getCategory"]);

    Route::get("types/{category}", [CompanyController::class, "getTypesSkills"]);

    Route::get("skills/{types}", [CompanyController::class, "getSkillName"]);

    Route::post("addoffer", [CompanyController::class, "addOfferWeb"]);

    Route::post("update-offer", [CompanyController::class, "offerUpdate"]);

    Route::get("get-offers/{company_id}", [CompanyController::class, "getOffers"]);

    Route::get("get-job-applicants/{offer_id}", [CompanyController::class, "getJobApplicants"]);

    Route::post("change-offer-state", []);

    Route::post("post", [CompanyController::class, "postWeb"]);

});



