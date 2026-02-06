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
            $table->string('code')->unique()->nullable()->after('end_date');
            $table->string('discount_type')->nullable()->after('code'); // e.g., 'fixed', 'percentage'
            $table->decimal('discount_value', 8, 2)->nullable()->after('discount_type'); // e.g., 100.00 or 0.10
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['code', 'discount_type', 'discount_value']);
        });
    }
};
