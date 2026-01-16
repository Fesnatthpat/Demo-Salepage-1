<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            // เพิ่มคอลัมน์เก็บส่วนลด (Decimal: 10 หลัก, ทศนิยม 2 ตำแหน่ง)
            // default 0 เผื่อสินค้านั้นไม่มีส่วนลด
            $table->decimal('pd_sp_discount', 10, 2)->default(0.00)->after('ordd_count');
        });
    }

    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            $table->dropColumn('pd_sp_discount');
        });
    }
};
