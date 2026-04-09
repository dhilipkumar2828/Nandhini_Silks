<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShiprocketService;

class ShippingCalculatorController extends Controller
{
    public function index()
    {
        return view('admin.shipping.calculator');
    }

    public function check(Request $request, ShiprocketService $shiprocket)
    {
        $request->validate([
            'pincode' => 'required|numeric|digits:6',
            'weight' => 'required|numeric|min:0.1',
            'cod' => 'required|boolean',
        ]);

        $result = $shiprocket->checkServiceability(
            $request->pincode,
            $request->weight,
            $request->cod
        );

        if ($result['status'] && isset($result['data']['available_courier_companies'])) {
            return response()->json([
                'success' => true,
                'data' => $result['data']['available_courier_companies'],
                'shiprocket_response' => $result // Full debug data
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Could not fetch shipping data.',
            'shiprocket_response' => $result // Full debug data
        ]);
    }

    public function track(Request $request, ShiprocketService $shiprocket)
    {
        $request->validate([
            'awb' => 'required|string',
        ]);

        $result = $shiprocket->trackByAWB($request->awb);

        // Shiprocket often returns data keyed by the AWB number { "12345": { "tracking_data": ... } }
        $trackingData = $result[$request->awb] ?? $result;

        if ($trackingData) {
            return response()->json([
                'success' => true,
                'data' => $trackingData,
                'shiprocket_raw' => $result // Full raw response for Postman-style checking
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tracking data not found or API error.'
        ]);
    }
}
