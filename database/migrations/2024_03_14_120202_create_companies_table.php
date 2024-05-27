<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("password");
            $table->string("email")->unique();
            $table->boolean("isActive")->default(false);
            $table->string("verificationCode")->nullable();
            $table->date("establishment_date");
            $table->string("type")->default("company");
            $table->unsignedInteger("employee_number");
            $table->string("profile_photo")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
