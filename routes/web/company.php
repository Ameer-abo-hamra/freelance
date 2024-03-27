<?php

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
route::post("register", [CompanyController::class, "register"]);
route::post("login", [CompanyController::class, "login"]);
    Route::group(["middleware"=> "check:web-company"],function (){

Route::get("category" , [CompanyController::class,"getCategory"]);
Route::get("types/{category}",[CompanyController::class,"getTypesSkills"]);

Route::get("skills/{types}",[CompanyController::class,"getSkillName"]);

Route::post("addoffer" , [CompanyController::class , "addOffer"]);
    });

