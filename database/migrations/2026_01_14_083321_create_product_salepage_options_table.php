<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // สร้างตารางกลาง
        Schema::create('product_salepage_options', function (Blueprint $table) {
            $table->id();
            
            // ID ของสินค้าหลัก
            $table->unsignedBigInteger('product_salepage_id');
            
            // ID ของสินค้าที่เป็นตัวเลือก (Option)
            $table->unsignedBigInteger('option_product_salepage_id');
            
            $table->timestamps();

            // (Optional) ถ้าต้องการทำ Foreign Key เพื่อความสมบูรณ์ของข้อมูล (Uncomment ได้)
            // $table->foreign('product_salepage_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
            // $table->foreign('option_product_salepage_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_salepage_options');
    }
};