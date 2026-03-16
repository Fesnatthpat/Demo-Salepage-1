<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tambons', function (Blueprint $table) {
            $table->id();
            $table->string('tambon', 191);
            $table->string('amphoe', 191);
            $table->string('province', 191);
            $table->string('zipcode', 10);
            $table->string('tambon_code', 10)->nullable();
            $table->string('amphoe_code', 10)->nullable();
            $table->string('province_code', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tambons');
    }
};
