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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("username")->unique();
            $table->string("full_name");
            $table->boolean("isActive")->default(false);
            $table->string("verificationCode")->nullable();
            $table->string("email");
            $table->string("password");
            $table->unsignedBigInteger("wallet")->default(10000);
            $table->string("profile_photo")->nullable();
            $table->date("birth_date");
            $table->string("type")->default("customer");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
