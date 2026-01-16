<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            // เพิ่ม user_id ต่อจาก ord_id (เพื่อให้รู้ว่ารายการนี้ของ User คนไหน)
            $table->unsignedBigInteger('user_id')->after('ord_id')->nullable();

            // (Optional) สร้าง Foreign Key ถ้าต้องการ
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
