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
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('min_order_value', 12, 2)->nullable()->after('discount_value');
            $table->integer('usage_limit')->nullable()->after('min_order_value');
            $table->integer('used_count')->default(0)->after('usage_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['min_order_value', 'usage_limit', 'used_count']);
        });
    }
};
