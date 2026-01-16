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
            // แก้ไขบรรทัดนี้: เปลี่ยนจาก string เป็น id() เพื่อให้เป็น Auto Increment (สร้างตัวเลขเองอัตโนมัติ)
            $table->id();

            $table->longText('cart_data');
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
