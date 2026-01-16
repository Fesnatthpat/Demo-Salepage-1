<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('product_salepage', function (Blueprint $table) {
        // เพิ่มคอลัมน์ is_bogo_active ต่อจาก is_recommended
        // กำหนด default เป็น 0 (false)
        $table->boolean('is_bogo_active')->default(0)->after('is_recommended');
    });
}

public function down()
{
    Schema::table('product_salepage', function (Blueprint $table) {
        $table->dropColumn('is_bogo_active');
    });
}
};
