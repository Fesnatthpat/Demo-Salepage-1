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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->index();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('ID ของ User (ไม่ใช่ Admin)');
            $table->string('path')->nullable();
            $table->date('visit_date')->index(); // เพื่อใช้นับรายวันแบบไม่ซ้ำ IP ได้ง่ายขึ้น
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
