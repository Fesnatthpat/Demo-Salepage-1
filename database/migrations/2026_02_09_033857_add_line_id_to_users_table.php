<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ line_id (ให้เป็น null ได้ เผื่อคนสมัครแบบปกติ)
            $table->string('line_id')->nullable()->unique()->after('email');

            // เพิ่ม avatar ด้วย (เผื่อเก็บรูปโปรไฟล์ไลน์) ถ้ามีแล้วไม่ต้องใส่บรรทัดนี้
            // $table->string('avatar')->nullable()->after('line_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('line_id');
            // $table->dropColumn('avatar');
        });
    }
};
