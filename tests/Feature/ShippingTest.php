<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    /** @test */
    public function test_it_can_search_domestic_destinations()
    {
        Http::fake([
            '*/destination/domestic-destination*' => Http::response([
                'meta' => [
                    'message' => 'Success Get Domestic Destinations',
                    'code' => 200,
                    'status' => 'success'
                ],
                'data' => [
                    [
                        'id' => 114,
                        'label' => 'Tanah Abang, Jakarta Pusat, DKI Jakarta',
                        'province_name' => 'DKI Jakarta',
                        'city_name' => 'Jakarta Pusat',
                        'district_name' => 'Tanah Abang',
                        'subdistrict_name' => 'Tanah Abang',
                        'zip_code' => '10210'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson('/shipping/destinations?search=Tanah Abang');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.label', 'Tanah Abang, Jakarta Pusat, DKI Jakarta');
    }

    /** @test */
    public function test_it_can_calculate_domestic_shipping_cost()
    {
        Http::fake([
            '*/calculate/domestic-cost*' => Http::response([
                'meta' => [
                    'message' => 'Success Calculate Domestic Shipping cost',
                    'code' => 200,
                    'status' => 'success'
                ],
                'data' => [
                    [
                        'name' => 'JNE',
                        'code' => 'jne',
                        'service' => 'REG',
                        'description' => 'Layanan Reguler',
                        'cost' => 10000,
                        'etd' => '1-2'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/shipping/calculate', [
            'origin' => 114,
            'destination' => 200,
            'weight' => 1000,
            'courier' => 'jne',
            'price' => 'lowest'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.code', 'jne')
            ->assertJsonPath('data.0.cost', 10000);
    }

    /** @test */
    public function test_it_validates_shipping_calculation_request()
    {
        $response = $this->postJson('/shipping/calculate', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['origin', 'destination', 'weight', 'courier']);
    }
}
