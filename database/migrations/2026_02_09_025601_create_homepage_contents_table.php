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
        Schema::create('homepage_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_name')->index(); // e.g., 'hero_slides', 'reasons', 'service_bar'
            $table->string('item_key')->nullable(); // A unique key within a section, e.g., 'slide_1', 'reason_1'
            $table->string('type'); // e.g., 'image', 'text', 'icon', 'link', 'collection'
            $table->text('value')->nullable(); // Main content for simple types (image URL, text)
            $table->json('data')->nullable(); // JSON for complex items (title, description, icon for reasons; name, image, link for menu items)
            $table->integer('order')->nullable(); // Display order
            $table->boolean('is_active')->default(true); // To enable/disable content items
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_contents');
    }
};
