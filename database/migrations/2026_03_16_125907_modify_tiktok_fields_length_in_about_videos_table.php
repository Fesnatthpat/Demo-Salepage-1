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
        Schema::table('about_videos', function (Blueprint $table) {
            // เปลี่ยน thumbnail_url เป็น text เพื่อรองรับ url ที่ยาวมาก
            $table->text('thumbnail_url')->nullable()->change();
            
            // เปลี่ยน embed_html เป็น longText เพื่อความชัวร์ในการเก็บ html จาก TikTok
            $table->longText('embed_html')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_videos', function (Blueprint $table) {
            // หากต้องการ rollback (อาจะมีปัญหาเรื่องข้อมูลถ้าข้อมูลจริงยาวกว่า 255)
            $table->string('thumbnail_url', 255)->nullable()->change();
            $table->text('embed_html')->nullable()->change();
        });
    }
};
