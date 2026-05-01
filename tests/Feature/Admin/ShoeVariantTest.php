<?php

namespace Tests\Feature\Admin;

use App\Models\ShoeModel;
use App\Models\ShoeVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShoeVariantTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $shoeModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->admin = User::factory()->create();
        $this->shoeModel = ShoeModel::create([
            'name' => 'Test Model',
            'slug' => 'test-model',
        ]);
    }

    public function test_admin_can_view_variants_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.model-sepatu.variants', $this->shoeModel));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_and_see_variant_in_list()
    {
        // 1. Visit index, should be empty
        $response = $this->actingAs($this->admin)
            ->get(route('admin.model-sepatu.variants', $this->shoeModel));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/ShoeVariant/Index')
            ->has('variants', 0)
        );

        // 2. Create variant
        $this->actingAs($this->admin)
            ->post(route('admin.model-sepatu.variants.store', $this->shoeModel), [
                'name' => 'Varian Baru 123',
            ]);

        // 3. Visit index again, should have 1 variant
        $response = $this->actingAs($this->admin)
            ->get(route('admin.model-sepatu.variants', $this->shoeModel));
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/ShoeVariant/Index')
            ->has('variants', 1)
            ->where('variants.0.name', 'Varian Baru 123')
        );
    }

    public function test_admin_can_bulk_upload_svgs()
    {
        Storage::fake('public_path');

        $variant = $this->shoeModel->variants()->create(['name' => 'Varian 1']);

        $file1 = UploadedFile::fake()->create('_base.svg', 100, 'image/svg+xml');
        $file2 = UploadedFile::fake()->create('_aksen1.svg', 100, 'image/svg+xml');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.variants.svgs.upload', $variant), [
                'files' => [$file1, $file2],
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('shoe_variant_svgs', [
            'shoe_variant_id' => $variant->id,
            'file_name' => '_base.svg',
        ]);
        
        $this->assertDatabaseHas('shoe_variant_svgs', [
            'shoe_variant_id' => $variant->id,
            'file_name' => '_aksen1.svg',
        ]);

        Storage::disk('public_path')->assertExists("shoes-svg/test-model/{$variant->id}/_base.svg");
        Storage::disk('public_path')->assertExists("shoes-svg/test-model/{$variant->id}/_aksen1.svg");
    }

    public function test_admin_can_delete_variant()
    {
        Storage::fake('public_path');
        
        $variant = $this->shoeModel->variants()->create(['name' => 'To Be Deleted']);
        $variant->svgs()->create([
            'file_name' => 'test.svg',
            'file_path' => "shoes-svg/test-model/{$variant->id}/test.svg",
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.variants.destroy', $variant));

        $response->assertRedirect();
        $this->assertDatabaseMissing('shoe_variants', ['id' => $variant->id]);
        $this->assertDatabaseMissing('shoe_variant_svgs', ['shoe_variant_id' => $variant->id]);
    }
}
