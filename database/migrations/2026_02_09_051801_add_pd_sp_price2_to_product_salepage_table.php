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
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->decimal('pd_sp_price2', 10, 2)->nullable()->after('pd_sp_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->dropColumn('pd_sp_price2');
        });
    }
};
