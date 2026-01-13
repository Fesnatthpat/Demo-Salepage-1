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
            $table->enum('pd_sp_display_location', ['homepage', 'general'])->default('general')->after('pd_sp_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->dropColumn('pd_sp_display_location');
        });
    }
};
