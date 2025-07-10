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
        Schema::create('videos', function (Blueprint $table) {
        $table->id();
        $table->string('original_video');
        $table->string('transcoded_video_240')->nullable();
        $table->string('transcoded_video_360')->nullable();
        $table->string('transcoded_video_480')->nullable();
        $table->string('transcoded_video_720')->nullable();
        $table->string('transcoded_video_1080')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
