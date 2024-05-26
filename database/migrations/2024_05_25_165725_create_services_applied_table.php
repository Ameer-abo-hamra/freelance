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
        Schema::create('services_applied', function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->references("id")->on("services");
            $table->foreignId("serviceApply_id")->references("id")->on("service_applies");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_applied');
    }
};
