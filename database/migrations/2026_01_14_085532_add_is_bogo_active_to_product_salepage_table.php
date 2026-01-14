<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bogo_promotion_options', function (Blueprint $table) {
            $table->id();

            // ID สินค้าหลัก
            $table->unsignedBigInteger('product_salepage_id');

            // ID สินค้าที่เป็นของแถม/ตัวเลือก (ชื่อต้องตรงกับ Error)
            $table->unsignedBigInteger('free_option_product_id');

            $table->timestamps();

            // (Optional) foreign keys
            // $table->foreign('product_salepage_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
            // $table->foreign('free_option_product_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bogo_promotion_options');
    }
};
