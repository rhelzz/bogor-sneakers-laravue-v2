<?php

namespace App\Http\Controllers;

use App\Services\Shipping\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    protected RajaOngkirService $shippingService;

    public function __construct(RajaOngkirService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Search destinations.
     */
    public function searchDestinations(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3',
            'limit' => 'nullable|integer|max:100',
            'offset' => 'nullable|integer'
        ]);

        $result = $this->shippingService->searchDestinations(
            $request->search,
            $request->limit ?? 10,
            $request->offset ?? 0
        );

        return response()->json($result, $result['meta']['code'] ?? 200);
    }

    /**
     * Calculate shipping cost.
     */
    public function calculateCost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string',
            'price' => 'nullable|string|in:lowest,highest'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'message' => 'Validation Error',
                    'code' => 422,
                    'status' => 'error'
                ],
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->shippingService->calculateCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier,
            $request->price ?? 'lowest'
        );

        return response()->json($result, $result['meta']['code'] ?? 200);
    }
}
