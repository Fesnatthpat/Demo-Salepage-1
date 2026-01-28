<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            // ลบคอลัมน์ quantity ออก เพราะเราใช้ ordd_count แทนแล้ว
            if (Schema::hasColumn('order_detail', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            // สร้างคืนเผื่อ rollback (กำหนดประเภทเป็น integer)
            if (! Schema::hasColumn('order_detail', 'quantity')) {
                $table->integer('quantity')->nullable();
            }
        });
    }
};
