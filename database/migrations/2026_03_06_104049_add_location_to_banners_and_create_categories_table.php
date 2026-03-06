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
        // Add location to banners
        Schema::table('banners', function (Blueprint $table) {
            $table->string('location')->default('homepage')->after('type')->index();
        });

        // Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add category_id to products if not exists
        if (Schema::hasTable('product_salepages')) {
            Schema::table('product_salepages', function (Blueprint $table) {
                if (!Schema::hasColumn('product_salepages', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('pd_sp_id')->constrained('categories')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_salepages')) {
            Schema::table('product_salepages', function (Blueprint $table) {
                if (Schema::hasColumn('product_salepages', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }
        
        Schema::dropIfExists('categories');

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
