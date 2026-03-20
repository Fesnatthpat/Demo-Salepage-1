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
        Schema::table('homepage_popups', function (Blueprint $table) {
            // เพิ่มฟิลด์สำหรับระบุหน้าที่ต้องการแสดง เช่น ['home', 'product.show'] หรือ null ถ้าแสดงทุกหน้า
            $table->text('display_pages')->nullable()->after('display_type');
            $table->integer('sort_order')->default(0)->after('display_pages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_popups', function (Blueprint $table) {
            $table->dropColumn(['display_pages', 'sort_order']);
        });
    }
};
