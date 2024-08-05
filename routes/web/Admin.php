<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PostController;

Route::get("deleteUser/{type}/{id}", [AdminController::class, "deleteUser"]);

Route::get("deletePost/{post_id}", [PostController::class, "deletePostDirectly"]);

Route::post("addSkill", [SkillController::class, "addSkill"]);

Route::post("updateSkill", [SkillController::class, "updateSkill"]);

Route::get("deleteSkill/{id}", [SkillController::class, "deleteSkill"]);

Route::post("addType", [TypeController::class, "addType"]);

Route::post("updateType", [TypeController::class, "updateType"]);

Route::get("deleteType/{type_id}", [TypeController::class, "deleteType"]);

Route::post("addCategory", [CategoryController::class, "addCategory"]);

Route::post("updateCategory", [CategoryController::class, "updateCategory"]);

Route::get("deleteCategory/{id}", [CategoryController::class, "deleteCategory"]);

Route::get("showJobSeekers", [JobSeekerController::class, "showJob_seekers"]);

Route::get("showCompanies", [CompanyController::class, "showCompanies"]);

Route::get("showCustomers", [CustomerController::class, "showCustomers"]);

// Route::get("countReports/{post_id}",[ReportController::class,"countReports"]);

Route::get("getReports", [AdminController::class, "showAllReports"]);

Route::get("showAllServices",[AdminController::class,"showAllServices"]);

Route::post("updateServiceState/{id}", [AdminController::class, "updateServiceStatus"]);

Route::get("viewProfile/{id}",[AdminController::class,"profile"]);

Route::post("updateProfile",[AdminController::class,"updateProfile"]);
