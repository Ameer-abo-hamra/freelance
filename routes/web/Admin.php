<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TypeController;
use Illuminate\Support\Facades\Route;

Route::get("delete/{type}/{id}", [AdminController::class, "deleteUser"]);

Route::get("deletePost/{id}",[AdminController::class,"deletePost"]);

Route::post("add-skill", [SkillController::class, "addSkill"]);

Route::post("updateSkill",[SkillController::class,"updateSkill"]);

Route::get("deleteSkill/{id}",[SkillController::class,"deleteSkill"]);

Route::post("addType",[TypeController::class,"addType"]);

Route::post("updateType",[TypeController::class,"updateType"]);

Route::get("deleteType/{id}",[TypeController::class,"deleteType"]);

Route::post("addCategory",[CategoryController::class,"addCategory"]);

// Route::post("report",[ReportController::class,"report"]);
