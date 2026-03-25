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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อบริษัทขนส่ง (Flash, Kerry)
            $table->string('code')->unique(); // รหัส (flash, kerry)
            $table->boolean('is_active')->default(true);
            $table->decimal('bkk_rate', 10, 2)->default(0); // ค่าส่ง กทม.
            $table->decimal('upc_rate', 10, 2)->default(0); // ค่าส่ง ตจว.
            $table->decimal('free_threshold', 10, 2)->nullable(); // ส่งฟรีเมื่อซื้อครบ
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert some defaults
        DB::table('shipping_methods')->insert([
            ['name' => 'Flash Express', 'code' => 'flash', 'bkk_rate' => 35, 'upc_rate' => 50, 'free_threshold' => 999, 'is_active' => true, 'created_at' => now()],
            ['name' => 'Kerry Express', 'code' => 'kerry', 'bkk_rate' => 45, 'upc_rate' => 60, 'free_threshold' => 1500, 'is_active' => true, 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
