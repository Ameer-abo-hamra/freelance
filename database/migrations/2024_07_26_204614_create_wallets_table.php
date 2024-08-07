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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("company_id")->references("id")->on("companies")->nullable();
            $table->foreignId("job_seeker_id")->references("id")->on("job_seekers")->nullable();
            $table->foreignId("customer_id")->references("id")->on("customers")->nullable();
            $table->unsignedBigInteger("balance");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
