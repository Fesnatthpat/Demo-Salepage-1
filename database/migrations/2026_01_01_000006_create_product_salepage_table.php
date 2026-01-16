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
        Schema::create('product_salepage', function (Blueprint $table) {
            $table->id('pd_sp_id');
            $table->unsignedBigInteger('pd_id')->nullable();
            $table->string('pd_sp_code')->unique();
            $table->string('pd_sp_name');
            $table->text('pd_sp_description')->nullable();
            $table->decimal('pd_sp_price', 10, 2);
            $table->decimal('pd_sp_discount', 10, 2)->default(0);
            $table->integer('pd_sp_stock')->default(0);
            $table->boolean('pd_sp_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_salepage');
    }
};
