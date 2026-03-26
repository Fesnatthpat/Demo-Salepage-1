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
        // 1. Update roles table
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'role_key')) {
                $table->string('role_key')->unique()->after('id')->nullable();
            }
            if (!Schema::hasColumn('roles', 'permissions')) {
                $table->json('permissions')->nullable()->after('role_key');
            }
        });

        // Populate existing roles with keys
        DB::table('roles')->where('name', 'superadmin')->update(['role_key' => 'superadmin']);
        DB::table('roles')->where('name', 'admin')->update(['role_key' => 'admin']);
        
        // Make role_key non-nullable after population
        Schema::table('roles', function (Blueprint $table) {
            $table->string('role_key')->nullable(false)->change();
        });

        // 2. Update admins table foreign key
        Schema::table('admins', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['role_id']);
            
            // Re-add with RESTRICT
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['role_key', 'permissions']);
        });
    }
};
