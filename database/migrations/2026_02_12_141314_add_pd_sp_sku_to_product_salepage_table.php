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
            $table->string('pd_sp_SKU')->nullable()->after('pd_sp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->dropColumn('pd_sp_SKU');
        });
    }
};
