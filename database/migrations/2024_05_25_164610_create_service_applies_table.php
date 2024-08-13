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
        Schema::create('service_applies', function (Blueprint $table) {
            $table->id();
            $table->morphs("applyable");
            $table->foreignId("service_id")->references("id")->on("services");
            $table->string("offer");
            $table->boolean("isAccepted")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_applies');
    }
};
