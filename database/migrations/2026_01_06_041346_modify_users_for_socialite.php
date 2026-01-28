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
        Schema::table('users', function (Blueprint $table) {
            // Add line_id for Socialite login, make it nullable and unique.
            $table->string('line_id')->nullable()->unique()->after('id');

            // Add avatar for Socialite login, make it nullable.
            $table->string('avatar')->nullable()->after('email');

            // Make the password field nullable for users who log in via Socialite.
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('line_id');
            $table->dropColumn('avatar');
            $table->string('password')->nullable(false)->change();
        });
    }
};
