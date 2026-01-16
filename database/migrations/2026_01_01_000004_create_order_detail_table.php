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
            $table->unsignedBigInteger('pd_id'); // Assuming this is the product ID
            $table->integer('quantity');
            $table->decimal('pd_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->timestamps();
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
