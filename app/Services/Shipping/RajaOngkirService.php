<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
        $this->baseUrl = config('services.rajaongkir.base_url');
    }

    /**
     * Get list of supported couriers.
     *
     * @return array
     */
    public function getSupportedCouriers(): array
    {
        return config('services.rajaongkir.supported_couriers', []);
    }

    /**
     * Validate if a courier code is supported.
     *
     * @param string $code
     * @return bool
     */
    public function isValidCourier(string $code): bool
    {
        return array_key_exists($code, $this->getSupportedCouriers());
    }

    /**
     * Search for domestic destinations.
     *
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchDestinations(string $search, int $limit = 10, int $offset = 0): array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'key' => $this->apiKey
            ])->get("{$this->baseUrl}/destination/domestic-destination", [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RajaOngkir Search Error: ' . $e->getMessage());
            return [
                'meta' => ['code' => 500, 'status' => 'error', 'message' => 'Internal Server Error'],
                'data' => null
            ];
        }
    }

    /**
     * Calculate domestic shipping cost.
     *
     * @param int $origin
     * @param int $destination
     * @param int $weight Grams
     * @param string $courier
     * @param string $price 'lowest' or 'highest'
     * @return array
     */
    public function calculateCost(int $origin, int $destination, int $weight, string $courier, string $price = 'lowest'): array
    {
        try {
            $response = Http::withoutVerifying()->asForm()->withHeaders([
                'key' => $this->apiKey
            ])->post("{$this->baseUrl}/calculate/domestic-cost", [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => $price
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RajaOngkir Calculate Error: ' . $e->getMessage());
            return [
                'meta' => ['code' => 500, 'status' => 'error', 'message' => 'Internal Server Error'],
                'data' => null
            ];
        }
    }

    /**
     * Track AWB (Waybill).
     *
     * @param string $waybill
     * @param string $courier
     * @return array
     */
    public function trackAWB(string $waybill, string $courier): array
    {
        try {
            $response = Http::withoutVerifying()->asForm()->withHeaders([
                'key' => $this->apiKey
            ])->post("{$this->baseUrl}/waybill/track", [
                'waybill' => $waybill,
                'courier' => $courier
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RajaOngkir Track Error: ' . $e->getMessage());
            return [
                'meta' => ['code' => 500, 'status' => 'error', 'message' => 'Internal Server Error'],
                'data' => null
            ];
        }
    }
}
