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
        Schema::table('promotions', function (Blueprint $table) {
            $table->integer('priority')->default(0)->after('is_active')->comment('ลำดับความสำคัญ ยิ่งน้อยยิ่งทำก่อน');
            $table->boolean('is_stackable')->default(true)->after('priority')->comment('สามารถใช้ร่วมกับโปรโมชั่นอื่นได้หรือไม่');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn(['priority', 'is_stackable']);
        });
    }
};
