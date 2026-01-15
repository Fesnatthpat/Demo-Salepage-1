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
        Schema::create('custom_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_salepage_id')->constrained('product_salepage', 'pd_sp_id')->onDelete('cascade');
            $table->string('name');
            $table->unsignedInteger('qty')->default(1);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_gifts');
    }
};