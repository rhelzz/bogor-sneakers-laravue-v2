<?php

namespace Tests\Feature;

use App\Models\ShoeModel;
use App\Models\ShoeType;
use App\Models\ShoeVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;

class ShoeManagementTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public_path');
    }

    /** @test */
    public function test_it_can_create_a_shoe_type_for_a_model()
    {
        $model = ShoeModel::create(['name' => 'Aero', 'slug' => 'aero']);

        $response = $this->post(route('admin.model-sepatu.types.store', $model->id), [
            'name' => 'Rubber Sole',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoe_types', [
            'shoe_model_id' => $model->id,
            'name' => 'Rubber Sole',
            'slug' => 'rubber-sole',
        ]);
    }

    /** @test */
    public function test_it_can_create_a_variant_linked_to_a_type()
    {
        $model = ShoeModel::create(['name' => 'Aero', 'slug' => 'aero']);
        $type = $model->types()->create(['name' => 'Rubber', 'slug' => 'rubber']);

        $response = $this->post(route('admin.model-sepatu.variants.store', $model->id), [
            'name' => 'Black Rubber',
            'shoe_type_id' => $type->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoe_variants', [
            'shoe_model_id' => $model->id,
            'shoe_type_id' => $type->id,
            'name' => 'Black Rubber',
        ]);
    }

    /** @test */
    public function test_it_can_create_a_variant_without_a_type()
    {
        $model = ShoeModel::create(['name' => 'Casual', 'slug' => 'casual']);

        $response = $this->post(route('admin.model-sepatu.variants.store', $model->id), [
            'name' => 'Classic White',
            'shoe_type_id' => null,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoe_variants', [
            'shoe_model_id' => $model->id,
            'shoe_type_id' => null,
            'name' => 'Classic White',
        ]);
    }

    /** @test */
    public function test_it_can_move_all_variants_to_a_type()
    {
        $model = ShoeModel::create(['name' => 'Aero', 'slug' => 'aero']);
        $type = $model->types()->create(['name' => 'Rubber', 'slug' => 'rubber']);
        
        // Create 3 variants without type
        $model->variants()->createMany([
            ['name' => 'V1', 'shoe_type_id' => null],
            ['name' => 'V2', 'shoe_type_id' => null],
            ['name' => 'V3', 'shoe_type_id' => null],
        ]);

        $response = $this->post(route('admin.model-sepatu.variants.move-all', $model->id), [
            'from_type_id' => null,
            'to_type_id' => $type->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals(3, ShoeVariant::where('shoe_type_id', $type->id)->count());
    }

    /** @test */
    public function test_it_deletes_physical_directory_when_variant_is_deleted()
    {
        $model = ShoeModel::create(['name' => 'Aero', 'slug' => 'aero']);
        $variant = $model->variants()->create(['name' => 'V1']);
        
        // Mock some files
        $dir = "shoes-svg/{$model->slug}/{$variant->id}";
        Storage::disk('public_path')->put("{$dir}/test.svg", 'content');
        
        $this->assertTrue(Storage::disk('public_path')->exists("{$dir}/test.svg"));

        $response = $this->delete(route('admin.variants.destroy', $variant->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('shoe_variants', ['id' => $variant->id]);
        $this->assertFalse(Storage::disk('public_path')->exists($dir));
    }

    /** @test */
    public function test_it_deletes_all_variant_directories_when_type_is_deleted()
    {
        $model = ShoeModel::create(['name' => 'Aero', 'slug' => 'aero']);
        $type = $model->types()->create(['name' => 'Rubber', 'slug' => 'rubber']);
        
        $v1 = $model->variants()->create(['name' => 'V1', 'shoe_type_id' => $type->id]);
        $v2 = $model->variants()->create(['name' => 'V2', 'shoe_type_id' => $type->id]);

        $dir1 = "shoes-svg/{$model->slug}/{$v1->id}";
        $dir2 = "shoes-svg/{$model->slug}/{$v2->id}";
        
        Storage::disk('public_path')->put("{$dir1}/f1.svg", 'c');
        Storage::disk('public_path')->put("{$dir2}/f2.svg", 'c');

        $response = $this->delete(route('admin.shoe-types.destroy', $type->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('shoe_types', ['id' => $type->id]);
        $this->assertDatabaseMissing('shoe_variants', ['id' => $v1->id]);
        $this->assertDatabaseMissing('shoe_variants', ['id' => $v2->id]);
        
        $this->assertFalse(Storage::disk('public_path')->exists($dir1));
        $this->assertFalse(Storage::disk('public_path')->exists($dir2));
    }
}
