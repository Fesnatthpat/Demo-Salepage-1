<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_bogo_options', function (Blueprint $table) {
            $table->id();

            // ID ของสินค้าหลัก (ตัวที่ซื้อ)
            $table->unsignedBigInteger('parent_id');

            // ID ของสินค้าของแถม (ตัวที่แถม)
            $table->unsignedBigInteger('child_id');

            $table->timestamps();

            // สร้าง Foreign Key เชื่อมไปยังตาราง product_salepage
            // หมายเหตุ: ตรวจสอบว่า PK ของตารางสินค้าชื่อ 'pd_sp_id' ตามที่ Error แจ้งมา
            $table->foreign('parent_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
            $table->foreign('child_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_bogo_options');
    }
};
