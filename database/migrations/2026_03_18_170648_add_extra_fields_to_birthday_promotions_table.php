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
        Schema::table('birthday_promotions', function (Blueprint $table) {
            $table->string('card_image_path')->nullable()->after('image_path')->comment('รูปภาพการ์ดอวยพรจาก CEO');
            $table->string('discount_code')->nullable()->after('link_url')->comment('รหัสส่วนลดพิเศษ');
            $table->unsignedBigInteger('gift_product_id')->nullable()->after('discount_code')->comment('สินค้าของแถมฟรี');
            $table->decimal('discount_value', 10, 2)->default(0)->after('gift_product_id')->comment('มูลค่าส่วนลด (ถ้ามี)');

            $table->foreign('gift_product_id')->references('pd_sp_id')->on('product_salepage')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birthday_promotions', function (Blueprint $table) {
            $table->dropForeign(['gift_product_id']);
            $table->dropColumn(['card_image_path', 'discount_code', 'gift_product_id', 'discount_value']);
        });
    }
};
