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
        // 1. ปรับปรุงตาราง order_detail
        if (Schema::hasTable('order_detail')) {
            Schema::table('order_detail', function (Blueprint $table) {
                // เพิ่ม Index และ Foreign Key สำหรับ pd_id
                if (!Schema::hasIndex('order_detail', 'order_detail_pd_id_foreign')) {
                    $table->index('pd_id');
                }
                
                // เพิ่ม Index และ Foreign Key สำหรับ option_id
                if (Schema::hasColumn('order_detail', 'option_id') && !Schema::hasIndex('order_detail', 'order_detail_option_id_foreign')) {
                    $table->index('option_id');
                }

                // เพิ่ม Index และ Foreign Key สำหรับ user_id
                if (Schema::hasColumn('order_detail', 'user_id') && !Schema::hasIndex('order_detail', 'order_detail_user_id_foreign')) {
                    $table->index('user_id');
                }
            });
        }

        // 2. ปรับปรุงตาราง orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                // เพิ่ม Index ให้ status_id เพื่อการ Query ที่เร็วขึ้นในหน้า Admin
                if (Schema::hasColumn('orders', 'status_id')) {
                    $table->index('status_id');
                }
            });
        }

        // 3. ปรับปรุงตาราง product_options
        if (Schema::hasTable('product_options')) {
            Schema::table('product_options', function (Blueprint $table) {
                if (Schema::hasColumn('product_options', 'parent_id')) {
                    $table->index('parent_id');
                }
                
                if (Schema::hasColumn('product_options', 'option_active')) {
                    $table->index('option_active');
                }
            });
        }

        // 4. ปรับปรุงตาราง stock_product
        if (Schema::hasTable('stock_product')) {
            Schema::table('stock_product', function (Blueprint $table) {
                if (Schema::hasColumn('stock_product', 'pd_sp_id')) {
                    $table->index('pd_sp_id');
                }
                
                if (Schema::hasColumn('stock_product', 'option_id')) {
                    $table->index('option_id');
                }

                // ป้องกันสต็อกซ้ำซ้อนสำหรับสินค้าและตัวเลือกเดียวกัน
                if (Schema::hasColumn('stock_product', 'pd_sp_id') && Schema::hasColumn('stock_product', 'option_id')) {
                    // ลบข้อมูลที่ซ้ำซ้อนออกก่อนถ้ามี (Optional - สำหรับตอนรันจริง)
                    // $table->unique(['pd_sp_id', 'option_id'], 'unique_stock_per_option');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. คืนค่าตาราง order_detail
        if (Schema::hasTable('order_detail')) {
            Schema::table('order_detail', function (Blueprint $table) {
                $table->dropIndex(['pd_id']);
                if (Schema::hasColumn('order_detail', 'option_id')) $table->dropIndex(['option_id']);
                if (Schema::hasColumn('order_detail', 'user_id')) $table->dropIndex(['user_id']);
            });
        }

        // 2. คืนค่าตาราง orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'status_id')) $table->dropIndex(['status_id']);
            });
        }

        // 3. คืนค่าตาราง product_options
        if (Schema::hasTable('product_options')) {
            Schema::table('product_options', function (Blueprint $table) {
                if (Schema::hasColumn('product_options', 'parent_id')) $table->dropIndex(['parent_id']);
                if (Schema::hasColumn('product_options', 'option_active')) $table->dropIndex(['option_active']);
            });
        }

        // 4. คืนค่าตาราง stock_product
        if (Schema::hasTable('stock_product')) {
            Schema::table('stock_product', function (Blueprint $table) {
                if (Schema::hasColumn('stock_product', 'pd_sp_id')) $table->dropIndex(['pd_sp_id']);
                if (Schema::hasColumn('stock_product', 'option_id')) $table->dropIndex(['option_id']);
            });
        }
    }
};
