<?php

namespace Tests\Feature\Admin;

use App\Models\ShoeModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoeModelTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        // Create an admin user (assuming any user can access for now, or you might have roles)
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_view_shoe_models_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.model-sepatu'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_shoe_model()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->admin)
            ->post(route('admin.model-sepatu.store'), [
                'name' => 'Running Pro 2024',
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoe_models', [
            'name' => 'Running Pro 2024',
            'slug' => 'running-pro-2024',
        ]);
    }

    public function test_admin_can_update_shoe_model()
    {
        $model = ShoeModel::create([
            'name' => 'Old Model',
            'slug' => 'old-model',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.model-sepatu.update', $model), [
                'name' => 'Updated Model Name',
                'is_active' => false,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shoe_models', [
            'id' => $model->id,
            'name' => 'Updated Model Name',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_shoe_model()
    {
        $model = ShoeModel::create([
            'name' => 'To Be Deleted',
            'slug' => 'to-be-deleted',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.model-sepatu.destroy', $model));

        $response->assertRedirect();
        $this->assertDatabaseMissing('shoe_models', ['id' => $model->id]);
    }
}
