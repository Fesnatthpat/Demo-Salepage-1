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
        Schema::create('birthday_promotions', function (Blueprint $col) {
            $col->id();
            $col->string('title')->nullable();
            $col->text('message')->nullable();
            $col->string('image_path')->nullable();
            $col->string('link_url')->nullable();
            $col->unsignedBigInteger('promotion_id')->nullable();
            $col->boolean('is_active')->default(false);
            $col->timestamps();

            $col->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthday_promotions');
    }
};
