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
            $couriers = collect($result['data']['available_courier_companies'])->map(function($courier) use ($request) {
                $baseFreight = (float) ($courier['freight_charge'] ?? 0);
                $otherFees = (float) ($courier['other_charges'] ?? 0) 
                           + (float) ($courier['whatsapp_charges'] ?? 0)
                           + (float) ($courier['coverage_charges'] ?? 0)
                           + (float) ($courier['entry_tax'] ?? 0);
                
                $finalRate = $baseFreight + $otherFees;
                if ($request->cod) {
                    $finalRate += (float) ($courier['cod_charges'] ?? 0);
                }

                // Add 2-day buffer to EDD for consistency with storefront
                $edd = $courier['etd'] ?? '3-5 Days';
                if ($courier['etd']) {
                    try {
                        $edd = \Carbon\Carbon::parse($courier['etd'])->addDays(2)->format('M d, Y');
                    } catch (\Exception $e) {}
                }

                $courier['final_rate'] = $finalRate;
                $courier['buffered_edd'] = $edd;
                return $courier;
            });

            return response()->json([
                'success' => true,
                'data' => $couriers,
                'shiprocket_response' => $result
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
