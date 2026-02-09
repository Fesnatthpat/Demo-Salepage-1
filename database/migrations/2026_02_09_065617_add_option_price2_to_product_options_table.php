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
        Schema::table('product_options', function (Blueprint $table) {
            $table->decimal('option_price2', 10, 2)->nullable()->after('option_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_options', function (Blueprint $table) {
            $table->dropColumn('option_price2');
        });
    }
};
