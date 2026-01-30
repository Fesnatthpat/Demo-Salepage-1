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
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id('ordd_id');
            $table->foreignId('ord_id')->constrained('orders')->onDelete('cascade');
            $table->unsignedBigInteger('pd_id'); // Assuming it links to product_salepage
            $table->decimal('ordd_price', 10, 2);
            $table->integer('ordd_count');
            $table->decimal('ordd_discount', 10, 2)->default(0);
            $table->dateTime('ordd_create_date');
            $table->timestamps();

            // If product_salepage table exists and has pd_sp_id as primary key
            // $table->foreign('pd_id')->references('pd_sp_id')->on('product_salepage')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_detail');
    }
};
