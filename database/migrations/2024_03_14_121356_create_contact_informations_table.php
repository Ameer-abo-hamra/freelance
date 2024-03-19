<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_informations', function (Blueprint $table) {
            $table->id();
            $table->string("email")->unique();
            $table->unsignedBigInteger("phone")->unique();
            $table->string("address");
            $table->foreignId("company_id")->nullable()->references("id")->on("companies");
            $table->foreignId("job_seeker_id")->nullable()->references("id")->on("job_seekers");
            $table->foreignId("customer_id")->nullable()->references("id")->on("customers");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_informations');
    }
};
