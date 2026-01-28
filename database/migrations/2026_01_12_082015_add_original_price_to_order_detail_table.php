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
        Schema::table('order_detail', function (Blueprint $table) {
            if (! Schema::hasColumn('order_detail', 'ordd_original_price')) {
                if (Schema::hasColumn('order_detail', 'ordd_price')) {
                    $table->decimal('ordd_original_price', 10, 2)->after('ordd_price')->nullable();
                } else {
                    // Fallback if ordd_price doesn't exist, just add it
                    $table->decimal('ordd_original_price', 10, 2)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_detail', function (Blueprint $table) {
            if (Schema::hasColumn('order_detail', 'ordd_original_price')) {
                $table->dropColumn('ordd_original_price');
            }
        });
    }
};
