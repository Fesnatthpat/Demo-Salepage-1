<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            // เพิ่มคอลัมน์ ordd_create_date (เก็บวันที่และเวลา)
            // ใช้ nullable() เผื่อไว้กรณีข้อมูลเก่าไม่มีค่านี้
            $table->dateTime('ordd_create_date')->nullable()->after('pd_sp_discount');
        });
    }

    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            $table->dropColumn('ordd_create_date');
        });
    }
};
