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
            if (!Schema::hasColumn('product_salepage', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('pd_sp_id')->index();
                $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_salepage', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
