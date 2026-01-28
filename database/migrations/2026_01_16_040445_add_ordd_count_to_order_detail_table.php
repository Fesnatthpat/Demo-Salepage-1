<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            if (! Schema::hasColumn('order_detail', 'ordd_count')) {
                // เพิ่มคอลัมน์ ordd_count (จำนวนสินค้า)
                $table->integer('ordd_count')->default(1)->after('pd_id');
            }
        });
    }

    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            if (Schema::hasColumn('order_detail', 'ordd_count')) {
                $table->dropColumn('ordd_count');
            }
        });
    }
};
