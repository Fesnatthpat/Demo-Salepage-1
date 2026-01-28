<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();

            // ID ของสินค้าหลัก (Parent Product)
            $table->unsignedBigInteger('parent_id');

            // ID ของสินค้าที่เป็นตัวเลือก (Child Product / Option)
            $table->unsignedBigInteger('child_id');

            // ราคาที่จะบวกเพิ่มหรือลดลง (Price Modifier)
            // กำหนดเป็น decimal เพื่อรองรับทศนิยม (เช่น 10, 2) และ default 0
            $table->decimal('price_modifier', 10, 2)->default(0.00)->nullable();

            $table->timestamps();

            // (Optional) การทำ Foreign Key เพื่อให้ข้อมูลสอดคล้องกัน
            // หมายเหตุ: ตรวจสอบว่าตารางหลักชื่อ 'product_salepage' และ PK ชื่อ 'pd_sp_id' ตาม Error ก่อนหน้านี้หรือไม่
            $table->foreign('parent_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
            $table->foreign('child_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_options');
    }
};
