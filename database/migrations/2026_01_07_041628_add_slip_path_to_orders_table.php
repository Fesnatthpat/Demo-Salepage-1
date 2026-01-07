<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // เพิ่มคอลัมน์ slip_path ต่อจาก status_id
            // ต้องใส่ nullable() เพราะตอนสร้างออเดอร์ใหม่ๆ ยังไม่มีสลิป
            $table->string('slip_path')->nullable()->after('status_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('slip_path');
        });
    }
};
