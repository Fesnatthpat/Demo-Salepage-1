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
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default values
        DB::table('shipping_settings')->insert([
            ['key' => 'free_shipping_threshold', 'value' => '999', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bkk_flat_rate', 'value' => '40', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'upc_flat_rate', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
