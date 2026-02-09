<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations; // Use this
use Illuminate\Foundation\Testing\DatabaseTransactions; // Use this
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HomepageContent;
use App\Models\Admin; // Assuming you have an Admin model for authentication
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HomepageContentTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user and authenticate
        $this->admin = Admin::factory()->create([
        $this->admin = Admin::factory()->create([
            // 'username' is generated uniquely by the factory
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);
    }

    /**
     * Test updating a 'value' field of a HomepageContent item.
     *
     * @return void
     */
    public function test_can_update_homepage_content_value_field(): void
    {
        $this->actingAs($this->admin, 'admin'); // Authenticate as admin

        $content = HomepageContent::create([
            'section_name' => 'test_section',
            'item_key' => 'test_item',
            'type' => 'text',
            'value' => 'Original text',
            'is_active' => true,
        ]);

        $newValue = 'Updated text content';

        $response = $this->postJson(route('admin.homepage-content.updateValue', $content->id), [
            'field' => 'value',
            'new_value' => $newValue,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Content updated successfully.']);

        $this->assertDatabaseHas('homepage_contents', [
            'id' => $content->id,
            'value' => $newValue,
        ]);
    }

    /**
     * Test updating a nested 'data' field of a HomepageContent item.
     *
     * @return void
     */
    public function test_can_update_homepage_content_nested_data_field(): void
    {
        $this->actingAs($this->admin, 'admin'); // Authenticate as admin

        $originalData = ['title' => 'Original Title', 'description' => 'Original Description'];
        $content = HomepageContent::create([
            'section_name' => 'test_section',
            'item_key' => 'test_item_data',
            'type' => 'collection',
            'data' => $originalData,
            'is_active' => true,
        ]);

        $newTitle = 'New Updated Title';

        // Update 'data.title'
        $response = $this->postJson(route('admin.homepage-content.updateValue', $content->id), [
            'field' => 'data.title',
            'new_value' => $newTitle,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Content updated successfully.']);

        $updatedContent = HomepageContent::find($content->id);
        $this->assertEquals($newTitle, $updatedContent->data['title']);
        $this->assertEquals('Original Description', $updatedContent->data['description']); // Other data should remain
    }

    /**
     * Test updating an image field of a HomepageContent item via file upload.
     *
     * @return void
     */
    public function test_can_update_homepage_content_image_field_with_upload(): void
    {
        Storage::fake('public'); // Fake the public disk for testing file uploads
        $this->actingAs($this->admin, 'admin'); // Authenticate as admin

        $content = HomepageContent::create([
            'section_name' => 'test_images',
            'item_key' => 'hero_image_1',
            'type' => 'image',
            'value' => 'original/path/to/image.png',
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('new-image.jpg', 1000, 1000)->size(500);

        $response = $this->postJson(route('admin.homepage-content.updateValue', $content->id), [
            'field' => 'value',
            'image_file' => $newImage,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Image updated successfully.'])
            ->assertJsonStructure(['new_image_url']);

        $updatedContent = HomepageContent::find($content->id);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $updatedContent->value)); // Check if file exists
        $this->assertStringContainsString('homepage_images/', $updatedContent->value); // Check path format
        $this->assertNotEquals('original/path/to/image.png', $updatedContent->value); // Ensure path changed
    }

    /**
     * Test updating a nested image field (e.g., data.image) of a HomepageContent item via file upload.
     *
     * @return void
     */
    public function test_can_update_homepage_content_nested_image_field_with_upload(): void
    {
        Storage::fake('public'); // Fake the public disk
        $this->actingAs($this->admin, 'admin'); // Authenticate as admin

        $originalData = ['image' => 'original/path/to/nested_image.png', 'alt' => 'Original Alt'];
        $content = HomepageContent::create([
            'section_name' => 'test_images',
            'item_key' => 'nested_image_1',
            'type' => 'collection',
            'data' => $originalData,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('nested-new-image.jpg', 800, 600)->size(300);

        $response = $this->postJson(route('admin.homepage-content.updateValue', $content->id), [
            'field' => 'data.image', // Target the nested image field
            'image_file' => $newImage,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Image updated successfully.'])
            ->assertJsonStructure(['new_image_url']);

        $updatedContent = HomepageContent::find($content->id);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $updatedContent->data['image']));
        $this->assertStringContainsString('homepage_images/', $updatedContent->data['image']);
        $this->assertNotEquals('original/path/to/nested_image.png', $updatedContent->data['image']);
        $this->assertEquals('Original Alt', $updatedContent->data['alt']); // Other data should remain
    }
}
