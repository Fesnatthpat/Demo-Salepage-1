<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. สร้าง Role เริ่มต้นในตาราง roles
        $superAdminId = DB::table('roles')->insertGetId([
            'name' => 'superadmin',
            'level' => 100, // ระดับสูงสุด
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'level' => 50, // ระดับรองลงมา
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('admins', function (Blueprint $table) use ($superAdminId, $adminId) {
            // 2. เพิ่มคอลัมน์ role_id เป็น Foreign Key (ยอมให้เป็น null ชั่วคราวเพื่อย้ายข้อมูล)
            $table->foreignId('role_id')->after('password')->nullable()->constrained('roles')->onDelete('cascade');
        });

        // 3. ย้ายข้อมูลจากคอลัมน์ role เดิมไปยัง role_id
        DB::table('admins')->where('role', 'superadmin')->update(['role_id' => $superAdminId]);
        DB::table('admins')->where('role', 'admin')->update(['role_id' => $adminId]);
        // สำหรับกรณีที่มีค่าอื่น (ถ้ามี) ให้ default เป็น admin
        DB::table('admins')->whereNull('role_id')->update(['role_id' => $adminId]);

        Schema::table('admins', function (Blueprint $table) {
            // 4. ลบคอลัมน์ role เดิม
            $table->dropColumn('role');
            // 5. เปลี่ยน role_id ให้ห้ามเป็น null
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // 1. เพิ่มคอลัมน์ role กลับมาเป็น enum เหมือนเดิม
            $table->enum('role', ['admin', 'superadmin'])->default('admin')->after('password');
        });

        // 2. ย้ายข้อมูลกลับจาก role_id ไปยัง role
        $roles = DB::table('roles')->pluck('name', 'id');
        foreach ($roles as $id => $name) {
            DB::table('admins')->where('role_id', $id)->update(['role' => $name]);
        }

        Schema::table('admins', function (Blueprint $table) {
            // 3. ลบ Foreign Key และ role_id
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        // 4. ลบข้อมูลใน roles
        DB::table('roles')->truncate();
    }
};
