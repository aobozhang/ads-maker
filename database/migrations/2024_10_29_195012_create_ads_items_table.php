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
        Schema::create('ads_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->string('remote')->nullable();
            $table->json('tags')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('status')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_items');
    }
};
