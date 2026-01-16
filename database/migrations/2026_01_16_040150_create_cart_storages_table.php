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
        Schema::create('cart_storages', function (Blueprint $table) {
            // สร้าง ID เป็น String เพราะบางที Library ใช้ Session ID เป็น Key
            $table->string('id')->primary(); 
            
            // เก็บข้อมูลตะกร้า (Serialized Data หรือ JSON)
            $table->longText('cart_data'); 
            
            // รองรับการเก็บ User ID (อาจจะเป็น Null ได้ถ้าไม่ได้ Login)
            // ตาม Error ของคุณ query หา user_id ดังนั้นต้องมี column นี้
            $table->unsignedBigInteger('user_id')->nullable()->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_storages');
    }
};