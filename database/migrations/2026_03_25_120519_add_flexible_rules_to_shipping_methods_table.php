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
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->decimal('per_item_rate', 10, 2)->default(0)->after('upc_rate');
            $table->integer('min_items_for_free_shipping')->nullable()->after('free_threshold');
            $table->boolean('is_default')->default(false)->after('is_active');
            $table->text('description')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropColumn(['per_item_rate', 'min_items_for_free_shipping', 'is_default', 'description']);
        });
    }
};
