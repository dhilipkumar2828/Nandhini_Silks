<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShiprocketService
{
    protected string $baseUrl = 'https://apiv2.shiprocket.in/v1/external';
    protected ?string $email;
    protected ?string $password;

    public function __construct()
    {
        $this->email = config('services.shiprocket.email');
        $this->password = config('services.shiprocket.password');
    }

    /**
     * Get Authentication Token (Cached for 24 hours)
     */
    public function getToken()
    {
        if (Cache::has('shiprocket_token')) {
            return Cache::get('shiprocket_token');
        }

        try {
            $response = Http::post("{$this->baseUrl}/auth/login", [
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $token = $response->json('token');
                Cache::put('shiprocket_token', $token, now()->addHours(24));
                return $token;
            }

            Log::error('Shiprocket Auth Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Shiprocket Auth Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Push Order to Shiprocket
     */
    public function createOrder(Order $order)
    {
        $token = $this->getToken();
        if (!$token) {
            return ['status' => false, 'message' => 'Failed to authenticate with Shiprocket'];
        }

        $items = [];
        $totalWeight = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $items[] = [
                'name' => $item->product_name,
                'sku' => $product->sku ?? 'SKU-' . $product->id,
                'units' => $item->quantity,
                'selling_price' => $item->price,
                'discount' => 0,
                'tax' => 0,
                'hsn' => 0,
            ];
            $totalWeight += ($product->weight > 0 ? $product->weight : 0.5) * $item->quantity;
        }

        // Split name into first and last
        $nameParts = explode(' ', trim($order->customer_name), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '.';

        $payload = [
            'order_id' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => config('services.shiprocket.pickup_location', 'Primary'),
            'channel_id' => '',
            'comment' => 'Nandhini Silks Order',
            'billing_customer_name' => $firstName,
            'billing_last_name' => $lastName,
            'billing_address' => $order->delivery_address ?: 'No Address Provided',
            'billing_address_2' => '',
            'billing_city' => $order->shipping_city ?: 'Dharmapuri',
            'billing_pincode' => $order->shipping_pincode ?: '636352', 
            'billing_state' => $order->shipping_state ?: 'Tamil Nadu',
            'billing_country' => $order->shipping_country ?: 'India',
            'billing_email' => $order->customer_email,
            'billing_phone' => $order->customer_phone,
            'shipping_is_billing' => true,
            'order_items' => $items,
            'payment_method' => $order->payment_method === 'cod' ? 'COD' : 'Prepaid',
            'shipping_charges' => (float)$order->shipping,
            'giftwrap_charges' => 0,
            'transaction_parameters' => [
                'is_gift' => false,
                'gift_message' => ''
            ],
            'total_discount' => (float)$order->discount,
            'sub_total' => (float)$order->grand_total,
            'length' => 10,
            'breadth' => 10,
            'height' => 10,
            'weight' => (float)($totalWeight > 0 ? $totalWeight : 0.5),
        ];

        try {
            Log::info('Shiprocket Request Payload:', $payload);
            $response = Http::withToken($token)->post("{$this->baseUrl}/orders/create/adhoc", $payload);
            Log::info('Shiprocket Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $order->update([
                    'shiprocket_order_id' => $data['order_id'],
                    'shiprocket_shipment_id' => $data['shipment_id'],
                    'shiprocket_status' => 'NEW',
                ]);
                return ['status' => true, 'data' => $data];
            }

            Log::error('Shiprocket Order Creation Failed: ' . $response->body());
            return ['status' => false, 'message' => $response->json('message') ?? 'Unknown error'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Order Creation Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get Shipping Tracking Details
     */
    public function trackOrder($shipmentId)
    {
        $token = $this->getToken();
        if (!$token) return null;

        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/courier/track/shipment/{$shipmentId}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Shiprocket Tracking Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check Serviceability for a Delivery Pincode
     */
    public function checkServiceability($deliveryPincode, $weight = 0.5)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            Log::info('Shiprocket Serviceability Check:', [
                'pickup_postcode'   => config('services.shiprocket.pickup_pincode', '632317'), 
                'delivery_postcode' => $deliveryPincode,
            ]);

            $response = Http::withToken($token)->get("{$this->baseUrl}/courier/serviceability", [
                'pickup_postcode'   => config('services.shiprocket.pickup_pincode', '632317'), 
                'delivery_postcode' => $deliveryPincode,
                'weight'            => $weight,
                'cod'               => 1, 
            ]);

            Log::info('Shiprocket Serviceability Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json('data')];
            }

            return ['status' => false, 'message' => $response->json('message') ?? 'Pincode not serviceable'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Serviceability Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => 'API Error'];
        }
    }

    /**
     * Assign AWB (Tracking Number) to Shipment
     */
    public function assignAWB($shipmentId)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/courier/assign/awb", [
                'shipment_id' => $shipmentId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['response']['data']['awb_code'])) {
                    return ['status' => true, 'awb' => $data['response']['data']['awb_code']];
                }
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to assign AWB'];
        } catch (\Exception $e) {
            Log::error('Shiprocket AWB Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate Label for Shipment
     */
    public function generateLabel($shipmentId)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/courier/generate/label", [
                'shipment_id' => [$shipmentId],
            ]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to generate label'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Label Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Request Pickup for Shipment
     */
    public function requestPickup($shipmentId)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/courier/generate/pickup", [
                'shipment_id' => [$shipmentId],
            ]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to request pickup'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Pickup Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancel Order in Shiprocket
     */
    public function cancelOrder($shiprocketOrderId)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            $response = Http::withToken($token)->post("{$this->baseUrl}/orders/cancel", [
                'ids' => [$shiprocketOrderId],
            ]);

            Log::info('Shiprocket Cancel Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                return ['status' => true, 'message' => 'Order cancelled in Shiprocket'];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to cancel order in Shiprocket'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Cancel Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create Return Order in Shiprocket
     */
    public function createReturnOrder(Order $order)
    {
        $token = $this->getToken();
        if (!$token) return ['status' => false, 'message' => 'Auth Failed'];

        try {
            // Basic return order payload
            $payload = [
                'order_id'            => $order->order_number . '-RET',
                'order_date'          => now()->format('Y-m-d H:i'),
                'channel_id'          => "",
                'pickup_customer_name' => $order->customer_name,
                'pickup_last_name'    => "",
                'pickup_address'      => $order->delivery_address,
                'pickup_city'         => $order->shipping_city ?? 'Unnamed City',
                'pickup_state'        => $order->shipping_state ?? 'Unnamed State',
                'pickup_country'      => $order->shipping_country ?? 'India',
                'pickup_pincode'      => $order->shipping_pincode,
                'pickup_phone'        => $order->customer_phone,
                'shipping_customer_name' => config('app.name', 'Nandhini Silks'),
                'shipping_address'    => "Salem, Tamil Nadu, India",
                'shipping_city'       => "Salem",
                'shipping_state'      => "Tamil Nadu",
                'shipping_country'    => "India",
                'shipping_pincode'    => "636001",
                'shipping_phone'      => "9999999999",
                'order_items'         => [],
                'payment_method'      => "Prepaid",
                'total_weight'        => 0.5,
                'sub_total'           => (float) $order->sub_total,
                'length'              => 10,
                'breadth'             => 10,
                'height'              => 10,
            ];

            foreach ($order->items as $item) {
                $payload['order_items'][] = [
                    'sku'      => $item->product ? $item->product->sku : 'N-'.time(),
                    'name'     => $item->product_name,
                    'units'    => $item->quantity,
                    'selling_price' => (float) $item->price,
                ];
            }

            $response = Http::withToken($token)->post("{$this->baseUrl}/orders/create/return", $payload);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to create return order'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Return Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
