<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            // แก้ไขให้ตรงกับที่ Form ส่งมา (fullname, address_line1, etc.)
            $table->string('fullname');        // ชื่อ-นามสกุล
            $table->string('phone');           // เบอร์โทร
            $table->string('address_line1');   // บ้านเลขที่/หมู่บ้าน
            $table->string('address_line2')->nullable(); // ซอย/ถนน (เผื่อไม่มีให้ Nullable)

            // เก็บ ID ของจังหวัด/อำเภอ/ตำบล (น่าจะเป็น Integer)
            $table->integer('province_id');
            $table->integer('amphure_id');
            $table->integer('district_id');

            $table->string('zipcode');         // รหัสไปรษณีย์
            $table->text('note')->nullable();  // หมายเหตุ (Nullable)

            $table->boolean('is_primary')->default(false); // เก็บไว้เช็คว่าเป็นที่อยู่หลักไหม

            $table->timestamps();
            $table->softDeletes(); // รองรับการลบแบบกู้คืน

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
