<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::get("delete/{type}/{id}", [AdminController::class, "deleteUser"]);

// Route::post("add-skill", [SkillController::class, "addSkill"]);

Route::get("test", function () {

    return view("test")
    ;
});
