<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1, 2, 3. Banners Table (Hero, Secondary, and Info/Allergen Banner)
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['hero', 'secondary', 'info'])->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Service Bar Table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('icon'); // Stores 'fa-truck' or SVG string
            $table->string('title');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Why Choose Us (Features) Table
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('services');
        Schema::dropIfExists('features');
    }
};
