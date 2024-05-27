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
        Schema::create('job_seekers', function (Blueprint $table) {
            $table->id();
            $table->string("username")->unique();
            $table->string("full_name");
            $table->boolean("isActive")->default(false);
            $table->string("verificationCode")->nullable();
            $table->string("email")->unique();
            $table->string("password");
            $table->date("birth_date");
            $table->string("type")->default("job_seeker");
            $table->string("profile_photo")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers');
    }
};
