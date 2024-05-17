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
        Schema::create('job_seekers_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("job_seeker_id")->references("id")->on("job_seekers")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("offer_id")->references("id")->on("offers")->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean("isAccepted")->default(false);
            $table->string("CV");
            $table->text("additionalInfo")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_seekers_offers');
    }
};
