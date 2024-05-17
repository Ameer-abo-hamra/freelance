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
        Schema::create('company_job_seeker', function (Blueprint $table) {
            $table->id();
            $table->foreignId("job_seeker_id")->references("id")->on("job_seekers")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("company_id")->references("id")->on("companies")->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_job_seeker');
    }
};
