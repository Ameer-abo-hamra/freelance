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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("body");
            $table->foreignId("company_id")->nullable()->references("id")->on("companies")->cascadeOnDelete()->cascadeOnUpdate();;
            $table->foreignId("job_seeker_id")->nullable()->references("id")->on("job_seekers")->cascadeOnDelete()->cascadeOnUpdate();;
            $table->foreignId("post_id")->references("id")->on("job_seekers")->cascadeOnDelete()->cascadeOnUpdate();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
