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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("body");
            $table->string("photo")->nullable();
            $table->foreignId("job_seeker_id")->nullable()->references("id")->on("job_seekers")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("company_id")->nullable()->references("id")->on("companies")->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
