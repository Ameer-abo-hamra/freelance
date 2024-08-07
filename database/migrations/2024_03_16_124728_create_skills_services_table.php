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
        Schema::create('skills_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId("skill_id")->references("id")->on("skills")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("service_id")->references("id")->on("services")->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills_services');
    }
};
