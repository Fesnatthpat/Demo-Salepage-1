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
            // Drop the foreign key and column for child_id
            $table->dropForeign(['child_id']);
            $table->dropColumn('child_id');

            // Add new columns for storing option details directly
            $table->string('option_name')->nullable();
            $table->integer('option_stock')->default(0);
            $table->boolean('option_active')->default(true);

            // Rename price_modifier to option_price for clarity
            $table->renameColumn('price_modifier', 'option_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_options', function (Blueprint $table) {
            // Add back the child_id column and its foreign key
            $table->unsignedBigInteger('child_id');
            $table->foreign('child_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');

            // Drop the new columns
            $table->dropColumn(['option_name', 'option_stock', 'option_active']);

            // Rename option_price back to price_modifier
            $table->renameColumn('option_price', 'price_modifier');
        });
    }
};
